<?php

namespace App\Livewire;

use App\Models\Attachment;
use App\Models\Milestone;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskAssigned;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

#[Layout('layouts.app', ['title' => 'Create Task', 'breadcrumb' => 'Create Task'])]
class CreateTask extends Component
{
    use AuthorizesRequests, WithFileUploads;

    public ?int $project_id = null;

    public ?int $milestone_id = null;

    public string $title = '';

    public string $description = '';

    public ?int $assignee_id = null;

    public string $priority = 'medium';

    public string $due_date = '';

    public string $due_time = '';

    public bool $is_subtask = false;

    public ?int $parent_id = null;

    public ?int $story_points = null;

    public ?int $estimate_hours = null;

    public string $tagInput = '';

    /** @var array<int, string> */
    public array $tags = [];

    /** @var array<int, TemporaryUploadedFile> */
    public array $attachments = [];

    public bool $createAnother = false;

    public function mount(): void
    {
        abort_unless(auth()->user()->can('task.create'), 403);

        $firstProject = Project::where('status', 'active')->orderBy('name')->first();

        if ($requested = (int) request('project')) {
            $firstProject = Project::where('status', 'active')->find($requested) ?? $firstProject;
        }

        $this->project_id = $firstProject?->id;
        $this->assignee_id = auth()->id();
        $this->due_date = today()->addWeek()->toDateString();
    }

    public function updatedProjectId(): void
    {
        $this->milestone_id = null;
        $this->parent_id = null;
    }

    public function updatedIsSubtask(bool $value): void
    {
        if (! $value) {
            $this->parent_id = null;
        }
    }

    public function setPriority(string $priority): void
    {
        $this->priority = $priority;
    }

    public function addTag(): void
    {
        $tag = trim($this->tagInput, " \t\n\r\0\x0B#");

        if ($tag !== '' && ! in_array($tag, $this->tags, true)) {
            $this->tags[] = mb_substr($tag, 0, 40);
        }

        $this->tagInput = '';
    }

    public function removeTag(int $index): void
    {
        unset($this->tags[$index]);
        $this->tags = array_values($this->tags);
    }

    public function removeAttachment(int $index): void
    {
        unset($this->attachments[$index]);
        $this->attachments = array_values($this->attachments);
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'project_id' => ['required', 'integer', 'exists:projects,id'],
            'milestone_id' => ['nullable', 'integer', 'exists:milestones,id'],
            'title' => ['required', 'string', 'max:160'],
            'description' => ['nullable', 'string', 'max:10000'],
            'assignee_id' => [
                'nullable', 'integer', 'exists:users,id',
                // Assignee must be a member of the selected project.
                function ($attribute, $value, $fail) {
                    if ($value && $this->project_id) {
                        $isMember = DB::table('project_user')
                            ->where('project_id', $this->project_id)
                            ->where('user_id', $value)
                            ->exists();

                        if (! $isMember) {
                            $fail('The selected assignee is not a member of the chosen project.');
                        }
                    }
                },
            ],
            'priority' => ['required', 'in:low,medium,high,critical'],
            'due_date' => ['required', 'date', 'after_or_equal:today'],
            'due_time' => ['nullable', 'date_format:H:i'],
            'is_subtask' => ['boolean'],
            'parent_id' => ['nullable', 'integer', 'exists:tasks,id'],
            'story_points' => ['nullable', 'integer', 'min:0', 'max:100'],
            'estimate_hours' => ['nullable', 'integer', 'min:0', 'max:10000'],
            'tags' => ['array', 'max:10'],
            'tags.*' => ['string', 'max:40'],
            'attachments' => ['array', 'max:5'],
            'attachments.*' => ['file', 'max:25600', 'mimes:pdf,doc,docx,png,jpg,jpeg,zip'],
        ];
    }

    public function save(): mixed
    {
        $data = $this->validate();

        $user = auth()->user();

        if ($this->assignee_id && (int) $this->assignee_id !== $user->id && ! $user->can('task.assign')) {
            $this->addError('assignee_id', 'You may only assign tasks to yourself.');

            return null;
        }

        if ($this->milestone_id && ! Milestone::where('id', $this->milestone_id)->where('project_id', $this->project_id)->exists()) {
            $this->addError('milestone_id', 'Milestone does not belong to this project.');

            return null;
        }

        if ($this->is_subtask && $this->parent_id) {
            $parentValid = Task::where('id', $this->parent_id)
                ->where('project_id', $this->project_id)
                ->where('status', '!=', 'done')
                ->exists();

            if (! $parentValid) {
                $this->addError('parent_id', 'Parent task is invalid for this project.');

                return null;
            }
        }

        $task = Task::create([
            'project_id' => $this->project_id,
            'milestone_id' => $this->milestone_id,
            'parent_id' => $this->is_subtask ? $this->parent_id : null,
            'assignee_id' => $this->assignee_id,
            'created_by' => $user->id,
            'title' => $this->title,
            'description' => $this->description ?: null,
            'status' => 'todo',
            'priority' => $this->priority,
            'due_date' => $this->due_date,
            'due_time' => $this->due_time ?: null,
            'tags' => $this->tags ?: null,
            'story_points' => $this->story_points,
            'estimate_hours' => $this->estimate_hours,
        ]);

        foreach ($this->attachments as $upload) {
            /** @var TemporaryUploadedFile $upload */
            $path = $upload->store("attachments/{$task->id}", 'public');

            Attachment::create([
                'task_id' => $task->id,
                'user_id' => $user->id,
                'file_name' => $upload->getClientOriginalName(),
                'file_path' => $path,
                'mime_type' => $upload->getMimeType(),
                'file_size' => $upload->getSize(),
            ]);
        }

        session()->flash('success', "Task \"{$task->title}\" created successfully.");

        if ($task->assignee && $task->assignee->id !== $user->id) {
            $task->assignee->notify(new TaskAssigned($task, $user->name));
        }

        if ($this->createAnother) {
            $this->reset(['title', 'description', 'milestone_id', 'parent_id', 'is_subtask', 'tags', 'tagInput', 'attachments', 'story_points', 'estimate_hours']);

            return null;
        }

        return redirect()->route('tasks.mine');
    }

    public function render(): View
    {
        $projects = Project::where('status', 'active')->orderBy('name')->get();

        return view('livewire.create-task', [
            'projects' => $projects,
            'milestones' => Milestone::where('project_id', $this->project_id)->orderBy('title')->get(),
            'users' => User::orderBy('name')->get(),
            'parentTasks' => Task::with('project')
                ->where('project_id', $this->project_id)
                ->where('status', '!=', 'done')
                ->whereNull('parent_id')
                ->orderBy('title')
                ->limit(50)
                ->get(),
            'activeProject' => $projects->firstWhere('id', $this->project_id),
        ]);
    }
}
