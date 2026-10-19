<?php

namespace App\Livewire;

use App\Livewire\Concerns\NotifiesTaskFollowers;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app', ['title' => 'Task Board', 'breadcrumb' => 'Task Board'])]
class Board extends Component
{
    use AuthorizesRequests, NotifiesTaskFollowers;

    public string $project_id = 'all';

    public string $q = '';

    public string $assignee_id = 'all';

    public string $priority = 'all';

    public string $tag = 'all';

    public bool $overdueOnly = false;

    public function mount(): void
    {
        if ($requested = (string) request('project', '')) {
            $this->project_id = $requested;
        }
    }

    public function moveTask(int $taskId, string $status): void
    {
        $task = Task::with('project')->findOrFail($taskId);

        $this->authorize('updateStatus', $task);

        abort_unless(in_array($status, ['todo', 'in_progress', 'review', 'done'], true), 422);

        $task->status = $status;
        $task->completed_at = $status === 'done' ? now() : null;
        $task->save();

        $this->notifyStatusChange($task, auth()->user(), MyTasks::statusLabel($status));
    }

    /**
     * @return array<int, Project>
     */
    private function visibleProjects(): array
    {
        $user = auth()->user();

        return Project::where('status', 'active')
            ->when(! $user->can('create projects'), fn (Builder $q) => $q->whereHas('members', fn (Builder $m) => $m->whereKey($user->id)))
            ->orderBy('name')
            ->get()
            ->filter(fn (Project $p) => Gate::allows('view', $p))
            ->values()
            ->all();
    }

    private function baseQuery(array $visibleIds): Builder
    {
        return Task::with(['project', 'assignee'])
            ->withCount(['subtasks', 'subtasks as done_subtasks_count' => fn (Builder $q) => $q->where('status', 'done'), 'attachments', 'comments'])
            ->whereIn('project_id', $visibleIds)
            ->when($this->project_id !== 'all', fn (Builder $q) => $q->where('project_id', $this->project_id))
            ->when($this->assignee_id !== 'all', fn (Builder $q) => $q->where('assignee_id', $this->assignee_id))
            ->when($this->priority !== 'all', fn (Builder $q) => $q->where('priority', $this->priority))
            ->when($this->tag !== 'all', fn (Builder $q) => $q->whereJsonContains('tags', $this->tag))
            ->when($this->overdueOnly, fn (Builder $q) => $q->overdue())
            ->when($this->q !== '', function (Builder $q): void {
                $term = '%'.mb_strtolower($this->q).'%';
                $q->where(fn (Builder $w) => $w->whereRaw('lower(title) like ?', [$term])->orWhereRaw('lower(description) like ?', [$term]));
            });
    }

    public function render(): View
    {
        $projects = $this->visibleProjects();
        $visibleIds = array_map(fn (Project $p) => $p->id, $projects);

        if ($this->project_id !== 'all' && ! in_array((int) $this->project_id, $visibleIds, true)) {
            $this->project_id = 'all';
        }

        $columns = [];
        foreach (['todo' => 'To Do', 'in_progress' => 'In Progress', 'review' => 'Review', 'done' => 'Done'] as $status => $label) {
            $query = $this->baseQuery($visibleIds)->where('status', $status);

            $tasks = $status === 'done'
                ? $query->orderByDesc('completed_at')->limit(20)->get()
                : $query->orderBy('due_date')->limit(50)->get();

            $tasks->each(fn (Task $t) => $t->rowCanUpdate = Gate::allows('updateStatus', $t));
            $columns[] = ['status' => $status, 'label' => $label, 'tasks' => $tasks];
        }

        $scopeIds = $this->project_id === 'all' ? $visibleIds : [(int) $this->project_id];

        return view('livewire.board', [
            'projects' => $projects,
            'columns' => $columns,
            'assignees' => User::whereHas('assignedTasks', fn (Builder $q) => $q->whereIn('project_id', $scopeIds))->orderBy('name')->get(),
            'tags' => Task::whereIn('project_id', $scopeIds)->whereNotNull('tags')->pluck('tags')->flatten()->unique()->sort()->values(),
            'overdueCount' => $this->baseQuery($visibleIds)->overdue()->count(),
            'activeProject' => $this->project_id === 'all' ? null : Project::find($this->project_id),
            'canCreate' => auth()->user()->can('create tasks'),
        ]);
    }
}
