<?php

namespace App\Livewire;

use App\Models\Milestone;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\On;
use Livewire\Component;

class QuickCreateTask extends Component
{
    use AuthorizesRequests;

    public bool $open = false;

    public ?int $project_id = null;

    public ?int $milestone_id = null;

    public string $title = '';

    public string $description = '';

    public string $priority = 'medium';

    public string $due_date = '';

    public string $due_time = '';

    #[On('open-quick-create')]
    public function openModal(): void
    {
        $this->reset(['project_id', 'milestone_id', 'title', 'description', 'priority', 'due_date', 'due_time']);
        $this->priority = 'medium';
        $this->project_id = Project::where('status', 'active')->orderBy('name')->first()?->id;
        $this->due_date = today()->addWeek()->toDateString();
        $this->open = true;
    }

    public function close(): void
    {
        $this->open = false;
    }

    public function updatedProjectId(): void
    {
        $this->milestone_id = null;
    }

    public function setPriority(string $priority): void
    {
        $this->priority = $priority;
    }

    public function save(): void
    {
        $this->authorize('create', Task::class);

        $data = $this->validate([
            'project_id' => ['required', 'integer', 'exists:projects,id'],
            'milestone_id' => ['nullable', 'integer', 'exists:milestones,id'],
            'title' => ['required', 'string', 'max:160'],
            'description' => ['nullable', 'string', 'max:10000'],
            'priority' => ['required', 'in:low,medium,high,critical'],
            'due_date' => ['required', 'date', 'after_or_equal:today'],
            'due_time' => ['nullable', 'date_format:H:i'],
        ]);

        if ($data['milestone_id'] && ! Milestone::where('id', $data['milestone_id'])->where('project_id', $data['project_id'])->exists()) {
            $this->addError('milestone_id', 'Milestone does not belong to this project.');

            return;
        }

        $task = Task::create([
            'project_id' => $data['project_id'],
            'milestone_id' => $data['milestone_id'],
            'assignee_id' => auth()->id(),
            'created_by' => auth()->id(),
            'title' => $data['title'],
            'description' => $data['description'] ?: null,
            'status' => 'todo',
            'priority' => $data['priority'],
            'due_date' => $data['due_date'],
            'due_time' => $data['due_time'] ?: null,
        ]);

        $this->open = false;

        session()->flash('success', "Task \"{$task->title}\" created successfully.");

        $this->dispatch('task-created');
    }

    public function render(): View
    {
        return view('livewire.quick-create-task', [
            'projects' => Project::where('status', 'active')->orderBy('name')->get(),
            'milestones' => Milestone::where('project_id', $this->project_id)->orderBy('title')->get(),
        ]);
    }
}
