<?php

namespace App\Livewire;

use App\Models\Project;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app', ['title' => 'Projects', 'breadcrumb' => 'Projects'])]
class Projects extends Component
{
    use AuthorizesRequests;

    public string $tab = 'active';

    public string $q = '';

    public function setTab(string $tab): void
    {
        $this->tab = $tab;
    }

    public function openQuickCreate(): void
    {
        $this->dispatch('open-create-project');
    }

    public function archive(int $projectId): void
    {
        $project = Project::findOrFail($projectId);

        $this->authorize('archive', $project);

        $project->status = 'archived';
        $project->save();

        session()->flash('success', "Project \"{$project->name}\" archived.");
    }

    public function unarchive(int $projectId): void
    {
        $project = Project::findOrFail($projectId);

        $this->authorize('archive', $project);

        $project->status = 'active';
        $project->save();

        session()->flash('success', "Project \"{$project->name}\" reactivated.");
    }

    public function render(): View
    {
        $user = auth()->user();

        $projects = Project::with(['members'])
            ->withCount(['tasks', 'tasks as done_tasks_count' => fn ($q) => $q->where('status', 'done')])
            ->when($this->tab !== 'archived', fn ($q) => $q->where('status', 'active'), fn ($q) => $q->where('status', 'archived'))
            ->when(! $user->can('project.create'), fn ($q) => $q->whereHas('members', fn ($m) => $m->whereKey($user->id)))
            ->when($this->q !== '', function ($q): void {
                $term = '%'.mb_strtolower($this->q).'%';
                $q->where(fn ($w) => $w->whereRaw('lower(name) like ?', [$term])->orWhereRaw('lower(description) like ?', [$term]));
            })
            ->orderByDesc('updated_at')
            ->get()
            ->filter(fn (Project $p) => Gate::allows('view', $p))
            ->values();

        return view('livewire.projects', [
            'projects' => $projects,
            'activeCount' => Project::where('status', 'active')->count(),
            'archivedCount' => Project::where('status', 'archived')->count(),
            'canCreate' => $user->can('project.create'),
            'canArchive' => $user->can('project.archive'),
        ]);
    }
}
