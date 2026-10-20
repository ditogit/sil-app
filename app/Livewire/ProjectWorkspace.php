<?php

namespace App\Livewire;

use App\Models\Attachment;
use App\Models\Comment;
use App\Models\Milestone;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app', ['title' => 'Project Workspace', 'breadcrumb' => 'Project Workspace'])]
class ProjectWorkspace extends Component
{
    use AuthorizesRequests;

    public Project $project;

    public string $tab = 'ringkasan';

    public bool $mineOnly = false;

    // Settings: edit project
    public string $name = '';

    public string $description = '';

    public string $start_date = '';

    public string $end_date = '';

    // Members
    public ?int $new_member_id = null;

    // Milestone
    public string $milestoneTitle = '';

    public string $milestoneDue = '';

    public ?int $editingMilestoneId = null;

    public string $editingMilestoneTitle = '';

    public string $editingMilestoneDue = '';

    public function mount(Project $project): void
    {
        $this->authorize('view', $project);
        $this->project = $project;
        $this->syncEditState();
    }

    private function syncEditState(): void
    {
        $this->name = $this->project->name;
        $this->description = $this->project->description ?? '';
        $this->start_date = $this->project->start_date->toDateString();
        $this->end_date = $this->project->end_date?->toDateString() ?? '';
    }

    public function setTab(string $tab): void
    {
        $this->tab = $tab;
    }

    public function saveProject(): void
    {
        $this->authorize('update', $this->project);

        $data = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:10000'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        $this->project->fill([
            'name' => $data['name'],
            'description' => $data['description'] ?: null,
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'] ?: null,
        ])->save();

        session()->flash('success', 'Project updated.');
    }

    public function archive(): mixed
    {
        $this->authorize('archive', $this->project);

        $this->project->status = $this->project->status === 'archived' ? 'active' : 'archived';
        $this->project->save();

        session()->flash('success', $this->project->status === 'archived' ? 'Project archived.' : 'Project reactivated.');

        if ($this->project->status === 'archived') {
            return redirect()->route('projects.index');
        }

        return null;
    }

    public function addMember(): void
    {
        $this->authorize('manageMembers', $this->project);

        $this->validate(['new_member_id' => ['required', 'integer', 'exists:users,id']]);

        $this->project->members()->syncWithoutDetaching([$this->new_member_id]);
        $this->new_member_id = null;
    }

    public function removeMember(int $userId): void
    {
        $this->authorize('manageMembers', $this->project);

        if ($userId === $this->project->created_by) {
            $this->addError('members', 'The project creator cannot be removed.');

            return;
        }

        $this->project->members()->detach($userId);
    }

    public function addMilestone(): void
    {
        $this->authorize('update', $this->project);

        $this->validate([
            'milestoneTitle' => ['required', 'string', 'max:255'],
            'milestoneDue' => ['nullable', 'date'],
        ]);

        Milestone::create([
            'project_id' => $this->project->id,
            'title' => $this->milestoneTitle,
            'due_date' => $this->milestoneDue ?: null,
        ]);

        $this->milestoneTitle = '';
        $this->milestoneDue = '';
    }

    public function startEditMilestone(int $id): void
    {
        $this->authorize('update', $this->project);

        $milestone = Milestone::where('project_id', $this->project->id)->findOrFail($id);
        $this->editingMilestoneId = $milestone->id;
        $this->editingMilestoneTitle = $milestone->title;
        $this->editingMilestoneDue = $milestone->due_date?->toDateString() ?? '';
    }

    public function saveMilestone(): void
    {
        $this->authorize('update', $this->project);

        $milestone = Milestone::where('project_id', $this->project->id)->findOrFail($this->editingMilestoneId);

        $this->validate([
            'editingMilestoneTitle' => ['required', 'string', 'max:255'],
            'editingMilestoneDue' => ['nullable', 'date'],
        ]);

        $milestone->update([
            'title' => $this->editingMilestoneTitle,
            'due_date' => $this->editingMilestoneDue ?: null,
        ]);

        $this->editingMilestoneId = null;
    }

    public function deleteMilestone(int $id): void
    {
        $this->authorize('update', $this->project);

        Milestone::where('project_id', $this->project->id)->findOrFail($id)->delete();
    }

    public function render(): View
    {
        $user = auth()->user();
        $projectId = $this->project->id;

        $tasksQuery = Task::with(['assignee', 'milestone'])
            ->withCount(['subtasks', 'subtasks as done_subtasks_count' => fn (Builder $q) => $q->where('status', 'done'), 'attachments', 'comments'])
            ->where('project_id', $projectId);

        $allTasks = (clone $tasksQuery)->orderBy('due_date')->get();
        $allTasks->each(fn (Task $t) => $t->rowCanUpdate = Gate::allows('updateStatus', $t));

        $openTasks = $allTasks->where('status', '!=', 'done')->values();
        $doneTasks = $allTasks->where('status', 'done')->values();

        $members = $this->project->members()->orderBy('name')->get()->map(fn (User $m) => [
            'user' => $m,
            'count' => Task::where('project_id', $projectId)->where('assignee_id', $m->id)->open()->count(),
        ]);

        $criticalTasks = (clone $tasksQuery)->where('status', '!=', 'done')
            ->where(fn (Builder $q) => $q->overdue()->orWhereIn('priority', ['high', 'critical']))
            ->orderByRaw('due_date < ? desc', [today()->toDateString()])->orderBy('due_date')->limit(5)->get();
        $criticalTasks->each(fn (Task $t) => $t->rowCanUpdate = Gate::allows('updateStatus', $t));

        $recentAttachments = Attachment::with(['task', 'uploader'])
            ->whereHas('task', fn (Builder $q) => $q->where('project_id', $projectId))
            ->latest()->limit(4)->get();

        $recentComments = Comment::with(['author', 'task'])
            ->whereHas('task', fn (Builder $q) => $q->where('project_id', $projectId))
            ->latest()->limit(5)->get();

        $milestones = Milestone::withCount(['tasks', 'tasks as done_tasks_count' => fn (Builder $q) => $q->where('status', 'done')])
            ->where('project_id', $projectId)->orderBy('due_date')->orderBy('title')->get();

        $daftarTasks = $this->mineOnly
            ? $allTasks->where('assignee_id', $user->id)->values()
            : $allTasks;

        return view('livewire.project-workspace', [
            'allTasks' => $allTasks,
            'openTasks' => $openTasks,
            'doneTasks' => $doneTasks,
            'overdueCount' => $allTasks->filter(fn (Task $t) => $t->status !== 'done' && $t->due_date->lt(today()))->count(),
            'members' => $members,
            'criticalTasks' => $criticalTasks,
            'recentAttachments' => $recentAttachments,
            'recentComments' => $recentComments,
            'milestones' => $milestones,
            'daftarTasks' => $daftarTasks,
            'users' => User::orderBy('name')->get(),
            'canEdit' => Gate::allows('update', $this->project),
            'canArchive' => Gate::allows('archive', $this->project),
            'canManageMembers' => Gate::allows('manageMembers', $this->project),
            'canCreateTask' => $user->can('task.create'),
            'canDeleteTask' => $user->can('task.delete'),
        ]);
    }
}
