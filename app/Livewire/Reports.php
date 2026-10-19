<?php

namespace App\Livewire;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app', ['title' => 'Reports & Analytics', 'breadcrumb' => 'Reports & Analytics'])]
class Reports extends Component
{
    public string $period = '30';

    public string $project_id = 'all';

    public string $q = '';

    public function mount(): void
    {
        abort_unless(auth()->user()->can('view reports'), 403);
    }

    /**
     * @return array{start: Carbon, end: Carbon, label: string}
     */
    private function range(): array
    {
        $days = (int) $this->period;

        return [
            'start' => today()->subDays($days - 1)->startOfDay(),
            'end' => today()->endOfDay(),
            'label' => 'Last '.$days.' Days',
        ];
    }

    /**
     * @return Collection<int, Project>
     */
    private function visibleProjects(): Collection
    {
        return Project::where('status', 'active')
            ->when(! auth()->user()->can('create projects'), fn (Builder $q) => $q->whereHas('members', fn (Builder $m) => $m->whereKey(auth()->id())))
            ->orderBy('name')
            ->get()
            ->filter(fn (Project $p) => Gate::allows('view', $p))
            ->values();
    }

    /**
     * @return array<int>
     */
    private function visibleProjectIds(): array
    {
        return $this->visibleProjects()->pluck('id')->all();
    }

    private function scopedTasks(): Builder
    {
        $ids = $this->visibleProjectIds();

        return Task::whereIn('project_id', $ids)
            ->when($this->project_id !== 'all', fn (Builder $q) => $q->where('project_id', $this->project_id));
    }

    /**
     * @return array<string, mixed>
     */
    private function dataset(): array
    {
        $range = $this->range();
        $days = (int) $this->period;
        $base = $this->scopedTasks();

        $labels = [];
        $created = [];
        $completed = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $day = today()->subDays($i);
            $labels[] = $day->locale('en')->translatedFormat('d M');
            $created[] = (clone $base)->whereDate('created_at', $day)->count();
            $completed[] = (clone $base)->where('status', 'done')->whereDate('completed_at', $day)->count();
        }

        $statusCounts = [
            'todo' => (clone $base)->where('status', 'todo')->count(),
            'in_progress' => (clone $base)->where('status', 'in_progress')->count(),
            'review' => (clone $base)->where('status', 'review')->count(),
            'done' => (clone $base)->where('status', 'done')->count(),
        ];

        $priorityCounts = [
            'critical' => (clone $base)->where('priority', 'critical')->count(),
            'high' => (clone $base)->where('priority', 'high')->count(),
            'medium' => (clone $base)->where('priority', 'medium')->count(),
            'low' => (clone $base)->where('priority', 'low')->count(),
        ];

        $total = array_sum($statusCounts);
        $doneInPeriod = array_sum($completed);
        $createdInPeriod = array_sum($created);

        $workload = User::withCount(['assignedTasks' => fn (Builder $q) => $q->open()->whereIn('project_id', $this->visibleProjectIds())])
            ->orderByDesc('assigned_tasks_count')
            ->limit(20)
            ->get()
            ->filter(fn (User $u) => $u->assigned_tasks_count > 0)
            ->take(6)
            ->values();

        $projects = Project::with(['creator'])
            ->withCount(['tasks', 'tasks as done_tasks_count' => fn (Builder $q) => $q->where('status', 'done')])
            ->whereIn('id', $this->visibleProjectIds())
            ->when($this->q !== '', function (Builder $q): void {
                $term = '%'.mb_strtolower($this->q).'%';
                $q->where(fn (Builder $w) => $w->whereRaw('lower(name) like ?', [$term]));
            })
            ->orderByDesc('progress')
            ->get();

        $sla = [];
        foreach ($projects as $project) {
            $doneTotal = Task::where('project_id', $project->id)->where('status', 'done')->count();
            $onTime = Task::where('project_id', $project->id)->where('status', 'done')
                ->whereRaw('date(completed_at) <= due_date')->count();
            $sla[$project->id] = $doneTotal > 0 ? (int) round($onTime / $doneTotal * 100) : null;
        }

        return compact('labels', 'created', 'completed', 'statusCounts', 'priorityCounts', 'total', 'doneInPeriod', 'createdInPeriod', 'workload', 'projects', 'sla', 'range');
    }

    public function render(): View
    {
        return view('livewire.reports', array_merge($this->dataset(), [
            'projectOptions' => $this->visibleProjects(),
            'canExport' => auth()->user()->can('export reports'),
        ]));
    }
}
