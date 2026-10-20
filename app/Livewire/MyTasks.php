<?php

namespace App\Livewire;

use App\Livewire\Concerns\NotifiesTaskFollowers;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('layouts.app', ['title' => 'My Tasks', 'breadcrumb' => 'My Tasks'])]
class MyTasks extends Component
{
    use AuthorizesRequests, NotifiesTaskFollowers;

    public string $tab = 'all';

    public string $q = '';

    public string $project_id = 'all';

    public string $priority = 'all';

    public string $sort = 'due';

    public bool $showSubtasks = true;

    public function mount(): void
    {
        $this->q = (string) request('q', '');
    }

    public function setTab(string $tab): void
    {
        $this->tab = $tab;
    }

    public function openQuickCreate(): void
    {
        $this->dispatch('open-quick-create');
    }

    #[On('task-created')]
    public function refreshList(): void
    {
        //
    }

    public function setStatus(int $taskId, string $status): void
    {
        $task = Task::with('project')->findOrFail($taskId);

        $this->authorize('updateStatus', $task);

        abort_unless(in_array($status, ['todo', 'in_progress', 'review', 'done'], true), 422);

        $task->status = $status;
        $task->completed_at = $status === 'done' ? now() : null;
        $task->save();

        $this->notifyStatusChange($task, auth()->user(), self::statusLabel($status));

        session()->flash('success', "Status of \"{$task->title}\" changed to {$this->statusLabel($status)}.");
    }

    public function toggleDone(int $taskId): void
    {
        $task = Task::with('project')->findOrFail($taskId);

        $this->authorize('updateStatus', $task);

        if ($task->status === 'done') {
            $task->status = 'todo';
            $task->completed_at = null;
        } else {
            $task->status = 'done';
            $task->completed_at = now();
        }

        $task->save();

        $this->notifyStatusChange($task, auth()->user(), self::statusLabel($task->status));
    }

    public function deleteTask(int $taskId): void
    {
        $task = Task::with('project')->findOrFail($taskId);

        $this->authorize('delete', $task);

        $task->delete();

        session()->flash('success', "Task \"{$task->title}\" deleted.");
    }

    public static function statusLabel(string $status): string
    {
        return match ($status) {
            'todo' => 'To Do',
            'in_progress' => 'In Progress',
            'review' => 'Review',
            default => 'Done',
        };
    }

    private function baseQuery(): Builder
    {
        return Task::with(['project', 'milestone'])
            ->withCount(['subtasks', 'subtasks as done_subtasks_count' => fn (Builder $q) => $q->where('status', 'done'), 'attachments', 'comments'])
            ->where('assignee_id', auth()->id())
            ->when(! $this->showSubtasks, fn (Builder $q) => $q->whereNull('parent_id'))
            ->when($this->project_id !== 'all', fn (Builder $q) => $q->where('project_id', $this->project_id))
            ->when($this->priority !== 'all', fn (Builder $q) => $q->where('priority', $this->priority))
            ->when($this->q !== '', function (Builder $q): void {
                $term = '%'.mb_strtolower($this->q).'%';
                $q->where(fn (Builder $w) => $w
                    ->whereRaw('lower(title) like ?', [$term])
                    ->orWhereRaw('lower(description) like ?', [$term])
                    ->orWhereHas('project', fn (Builder $p) => $p->whereRaw('lower(name) like ?', [$term])));
            });
    }

    private function applySort(Builder $query): Builder
    {
        return match ($this->sort) {
            'priority' => $query->orderByRaw("case priority when 'critical' then 0 when 'high' then 1 when 'medium' then 2 else 3 end")->orderBy('due_date'),
            'updated' => $query->orderByDesc('updated_at'),
            'title' => $query->orderBy('title'),
            default => $query->orderByRaw('due_date < ? desc', [today()->toDateString()])->orderBy('due_date'),
        };
    }

    public function render(): View
    {
        $user = auth()->user();
        $base = $this->baseQuery();

        $counts = [
            'all' => (clone $base)->open()->count(),
            'today' => (clone $base)->open()->whereDate('due_date', today())->count(),
            'upcoming' => (clone $base)->open()->whereBetween('due_date', [today()->addDay(), today()->addDays(14)])->count(),
            'overdue' => (clone $base)->overdue()->count(),
            'done' => (clone $base)->where('status', 'done')->count(),
            'doneWeek' => (clone $base)->where('status', 'done')->whereBetween('completed_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
        ];

        $groups = [];
        $push = function (string $key, string $title, string $tone, $query, ?int $limit = null) use (&$groups): void {
            $tasks = $this->applySort($query);
            $tasks = $limit ? $tasks->limit($limit)->get() : $tasks->get();
            $tasks->each(fn (Task $t) => $t->rowCanUpdate = Gate::allows('updateStatus', $t));
            $groups[] = ['key' => $key, 'title' => $title, 'tone' => $tone, 'tasks' => $tasks];
        };

        $open = fn () => (clone $base)->open();

        match ($this->tab) {
            'today' => $push('today', 'Today — '.now()->locale('en')->translatedFormat('l, d M Y'), 'date', $open()->whereDate('due_date', today())),
            'upcoming' => $push('upcoming', 'Upcoming (This Week & Next Week)', 'default', $open()->whereBetween('due_date', [today()->addDay(), today()->addDays(14)])),
            'overdue' => $push('overdue', 'Overdue / Needs Immediate Action', 'red', (clone $base)->overdue()),
            'done' => $push('done', 'Recently Completed', 'green', (clone $base)->where('status', 'done')->orderByDesc('completed_at'), 20),
            default => (function () use ($push, $open, $base): void {
                $push('overdue', 'Overdue / Needs Immediate Action', 'red', (clone $base)->overdue());
                $push('today', 'Today — '.now()->locale('en')->translatedFormat('l, d M Y'), 'date', $open()->whereDate('due_date', today()));
                $push('upcoming', 'Upcoming (This Week & Next Week)', 'default', $open()->whereBetween('due_date', [today()->addDay(), today()->addDays(14)]));
                $push('done', 'Recently Completed', 'green', (clone $base)->where('status', 'done')->orderByDesc('completed_at'), 5);
            })(),
        };

        return view('livewire.my-tasks', [
            'counts' => $counts,
            'groups' => $groups,
            'myProjects' => Project::where('status', 'active')->orderBy('name')->get(),
            'canCreate' => $user->can('task.create'),
            'canDelete' => $user->can('task.delete'),
        ]);
    }
}
