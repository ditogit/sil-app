<?php

namespace App\Livewire;

use App\Livewire\Concerns\NotifiesTaskFollowers;
use App\Models\Comment;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app', ['title' => 'Dashboard', 'breadcrumb' => 'Main Dashboard'])]
class Dashboard extends Component
{
    use AuthorizesRequests, NotifiesTaskFollowers;

    public string $period = 'week';

    public string $priorityFilter = 'all';

    public bool $showAllMyTasks = false;

    public bool $showAllProjects = false;

    public function setPeriod(string $period): void
    {
        $this->period = $period;
    }

    public function cyclePeriod(): void
    {
        $this->period = match ($this->period) {
            'week' => 'month',
            'month' => 'today',
            default => 'week',
        };
    }

    public function cyclePriorityFilter(): void
    {
        $this->priorityFilter = match ($this->priorityFilter) {
            'all' => 'high',
            'high' => 'critical',
            'critical' => 'medium',
            'medium' => 'low',
            default => 'all',
        };
    }

    public function advanceStatus(int $taskId): void
    {
        $task = Task::with('project')->findOrFail($taskId);

        $this->authorize('updateStatus', $task);

        $task->status = match ($task->status) {
            'todo' => 'in_progress',
            'in_progress' => 'review',
            default => 'done',
        };

        $task->completed_at = $task->status === 'done' ? now() : null;
        $task->save();

        $this->notifyStatusChange($task, auth()->user(), MyTasks::statusLabel($task->status));
    }

    /**
     * @return array{start: Carbon, end: Carbon, label: string, previousStart: Carbon, previousEnd: Carbon}
     */
    private function periodRange(): array
    {
        $now = now();

        return match ($this->period) {
            'today' => [
                'start' => $now->copy()->startOfDay(),
                'end' => $now->copy()->endOfDay(),
                'label' => 'Today',
                'previousStart' => $now->copy()->subDay()->startOfDay(),
                'previousEnd' => $now->copy()->subDay()->endOfDay(),
            ],
            'month' => [
                'start' => $now->copy()->startOfMonth(),
                'end' => $now->copy()->endOfMonth(),
                'label' => 'This Month',
                'previousStart' => $now->copy()->subMonth()->startOfMonth(),
                'previousEnd' => $now->copy()->subMonth()->endOfMonth(),
            ],
            default => [
                'start' => $now->copy()->startOfWeek(),
                'end' => $now->copy()->endOfWeek(),
                'label' => 'This Week',
                'previousStart' => $now->copy()->subWeek()->startOfWeek(),
                'previousEnd' => $now->copy()->subWeek()->endOfWeek(),
            ],
        };
    }

    /**
     * @return Collection<int, array{icon: string, tint: string, text: string, task: string, meta: string, time: Carbon}>
     */
    private function recentActivities(): Collection
    {
        $comments = Comment::with(['author', 'task.project'])
            ->latest()
            ->limit(3)
            ->get()
            ->map(fn (Comment $comment) => [
                'icon' => 'messaging-message-circle',
                'tint' => 'bg-secondary-fixed text-on-secondary-fixed-variant',
                'text' => "{$comment->author->name} commented on",
                'task' => $comment->task->title,
                'meta' => $comment->task->project->name,
                'time' => $comment->created_at,
            ]);

        $completions = Task::with(['assignee', 'creator', 'project'])
            ->where('status', 'done')
            ->orderByDesc('completed_at')
            ->limit(2)
            ->get()
            ->map(fn (Task $task) => [
                'icon' => 'indicators-square-check',
                'tint' => 'bg-tertiary-fixed/40 text-on-tertiary-fixed-variant',
                'text' => ($task->assignee?->name ?? $task->creator?->name ?? 'Someone').' completed a task',
                'task' => $task->title,
                'meta' => $task->project->name,
                'time' => $task->completed_at ?? $task->updated_at,
            ]);

        return $comments
            ->concat($completions)
            ->sortByDesc('time')
            ->take(5)
            ->values();
    }

    public function render(): View
    {
        $user = auth()->user();
        $range = $this->periodRange();

        $myTasksQuery = Task::with(['project', 'milestone'])
            ->where('assignee_id', $user->id)
            ->where('status', '!=', 'done')
            ->when($this->priorityFilter !== 'all', fn ($query) => $query->where('priority', $this->priorityFilter))
            ->orderByRaw('due_date < ? desc', [today()->toDateString()])
            ->orderBy('due_date');

        $myActiveCount = (clone $myTasksQuery)->count();
        $myTasks = $this->showAllMyTasks ? $myTasksQuery->get() : $myTasksQuery->limit(5)->get();
        $myTasks->each(fn (Task $task) => $task->canUpdateStatus = Gate::allows('updateStatus', $task));

        $projectsQuery = Project::with(['members', 'tasks', 'milestones'])
            ->where('status', 'active')
            ->orderByDesc('progress');
        $projectsTotal = (clone $projectsQuery)->count();
        $projects = $this->showAllProjects ? $projectsQuery->get() : $projectsQuery->limit(3)->get();
        $projects = $projects->filter(fn (Project $p) => Gate::allows('view', $p))->values();

        $deadlines = Task::with(['project', 'assignee'])
            ->where('status', '!=', 'done')
            ->where(fn ($query) => $query->overdue()->orWhere(fn ($q) => $q->where('due_date', '<=', today()->addDays(3))))
            ->orderByRaw('due_date < ? desc', [today()->toDateString()])
            ->orderBy('due_date')
            ->limit(4)
            ->get();

        $workload = User::withCount(['assignedTasks' => fn ($query) => $query->open()])
            ->orderByDesc('assigned_tasks_count')
            ->limit(4)
            ->get()
            ->map(fn (User $member) => [
                'user' => $member,
                'count' => $member->assigned_tasks_count,
                'percent' => min(100, (int) round($member->assigned_tasks_count / 8 * 100)),
                'isFull' => $member->assigned_tasks_count >= 7,
            ]);

        $hour = (int) now()->format('H');

        return view('livewire.dashboard', [
            'greeting' => match (true) {
                $hour < 11 => 'Good morning',
                $hour < 15 => 'Good afternoon',
                $hour < 19 => 'Good evening',
                default => 'Good night',
            },
            'userName' => strtok($user->name, ' '),
            'userRole' => $user->getRoleNames()->first(),
            'todayLabel' => now()->locale('en')->translatedFormat('l, d F Y'),
            'periodLabel' => $range['label'],
            'totalActive' => Task::open()->count(),
            'overdueCount' => Task::overdue()->count(),
            'doneInPeriod' => Task::where('status', 'done')->whereBetween('completed_at', [$range['start'], $range['end']])->count(),
            'newDelta' => Task::whereBetween('created_at', [$range['start'], $range['end']])->count()
                - Task::whereBetween('created_at', [$range['previousStart'], $range['previousEnd']])->count(),
            'avgProgress' => (int) round(Project::where('status', 'active')->avg('progress') ?? 0),
            'activeProjectCount' => Project::where('status', 'active')->count(),
            'onTrackCount' => Project::where('status', 'active')->where('progress', '>=', 70)->count(),
            'myTasks' => $myTasks,
            'myActiveCount' => $myActiveCount,
            'projects' => $projects,
            'projectsTotal' => $projectsTotal,
            'deadlines' => $deadlines,
            'workload' => $workload,
            'activities' => $this->recentActivities(),
            'canCreateTask' => $user->can('task.create'),
            'priorityFilterLabel' => match ($this->priorityFilter) {
                'high' => 'High',
                'critical' => 'Critical',
                'medium' => 'Medium',
                'low' => 'Low',
                default => 'All Priorities',
            },
        ]);
    }
}
