<?php

namespace App\Livewire;

use App\Livewire\Concerns\NotifiesTaskFollowers;
use App\Models\Attachment;
use App\Models\Comment;
use App\Models\Milestone;
use App\Models\Task;
use App\Models\User;
use App\Notifications\CommentAdded;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

#[Layout('layouts.app', ['title' => 'Task Details', 'breadcrumb' => 'Task Details'])]
class TaskDetail extends Component
{
    use AuthorizesRequests, NotifiesTaskFollowers, WithFileUploads;

    public Task $task;

    public string $tab = 'comments';

    public bool $editing = false;

    public string $title = '';

    public string $description = '';

    public string $priority = 'medium';

    public string $due_date = '';

    public string $due_time = '';

    public ?int $milestone_id = null;

    public ?int $assignee_id = null;

    public string $commentBody = '';

    public bool $showAddSubtask = false;

    public string $subtaskTitle = '';

    /** @var array<int, TemporaryUploadedFile> */
    public array $uploads = [];

    public function mount(Task $task): void
    {
        $this->authorize('view', $task);

        $this->task = $task;
        $this->syncEditState();

        // Visiting the detail page marks this task's notifications as read.
        auth()->user()->unreadNotifications
            ->filter(fn ($notification) => (int) ($notification->data['task_id'] ?? 0) === $task->id)
            ->each->markAsRead();
    }

    private function syncEditState(): void
    {
        $this->title = $this->task->title;
        $this->description = $this->task->description ?? '';
        $this->priority = $this->task->priority;
        $this->due_date = $this->task->due_date->toDateString();
        $this->due_time = $this->task->due_time?->format('H:i') ?? '';
        $this->milestone_id = $this->task->milestone_id;
        $this->assignee_id = $this->task->assignee_id;
    }

    public function setStatus(string $status): void
    {
        $this->authorize('updateStatus', $this->task);

        abort_unless(in_array($status, ['todo', 'in_progress', 'review', 'done'], true), 422);

        $this->task->status = $status;
        $this->task->completed_at = $status === 'done' ? now() : null;
        $this->task->save();

        $this->notifyStatusChange($this->task, auth()->user(), self::statusLabel($status));
    }

    public function startEdit(): void
    {
        $this->authorize('update', $this->task);
        $this->syncEditState();
        $this->editing = true;
    }

    public function saveEdit(): void
    {
        $this->authorize('update', $this->task);

        $data = $this->validate([
            'title' => ['required', 'string', 'max:160'],
            'description' => ['nullable', 'string', 'max:10000'],
            'priority' => ['required', 'in:low,medium,high,critical'],
            'due_date' => ['required', 'date'],
            'due_time' => ['nullable', 'date_format:H:i'],
            'milestone_id' => ['nullable', 'integer', 'exists:milestones,id'],
            'assignee_id' => ['nullable', 'integer', 'exists:users,id'],
        ]);

        if ((int) ($data['assignee_id'] ?? 0) !== (int) $this->task->assignee_id
            && (int) ($data['assignee_id'] ?? 0) !== auth()->id()
            && ! auth()->user()->can('assign tasks')
        ) {
            $this->addError('assignee_id', 'You may only assign tasks to yourself.');

            return;
        }

        if ($data['milestone_id'] && ! Milestone::where('id', $data['milestone_id'])->where('project_id', $this->task->project_id)->exists()) {
            $this->addError('milestone_id', 'Milestone does not belong to this project.');

            return;
        }

        $this->task->fill([
            'title' => $data['title'],
            'description' => $data['description'] ?: null,
            'priority' => $data['priority'],
            'due_date' => $data['due_date'],
            'due_time' => $data['due_time'] ?: null,
            'milestone_id' => $data['milestone_id'],
            'assignee_id' => $data['assignee_id'],
        ])->save();

        $this->editing = false;
        session()->flash('success', 'Task changes saved.');
    }

    public function toggleSubtask(int $subtaskId): void
    {
        $subtask = Task::with('project')->findOrFail($subtaskId);

        abort_unless($subtask->parent_id === $this->task->id, 422);
        $this->authorize('updateStatus', $subtask);

        if ($subtask->status === 'done') {
            $subtask->status = 'todo';
            $subtask->completed_at = null;
        } else {
            $subtask->status = 'done';
            $subtask->completed_at = now();
        }

        $subtask->save();

        $this->notifyStatusChange($subtask, auth()->user(), self::statusLabel($subtask->status));
    }

    public function addSubtask(): void
    {
        abort_unless(auth()->user()->can('create tasks'), 403);

        $this->validate(['subtaskTitle' => ['required', 'string', 'max:160']]);

        Task::create([
            'project_id' => $this->task->project_id,
            'parent_id' => $this->task->id,
            'assignee_id' => $this->task->assignee_id,
            'created_by' => auth()->id(),
            'title' => $this->subtaskTitle,
            'status' => 'todo',
            'priority' => 'medium',
            'due_date' => $this->task->due_date,
        ]);

        $this->subtaskTitle = '';
        $this->showAddSubtask = false;
    }

    public function sendComment(): void
    {
        abort_unless(auth()->user()->can('create comments'), 403);

        $this->validate(['commentBody' => ['required', 'string', 'max:5000']]);

        Comment::create([
            'task_id' => $this->task->id,
            'user_id' => auth()->id(),
            'body' => trim($this->commentBody),
        ]);

        $author = auth()->user();
        $recipients = collect([$this->task->assignee, $this->task->creator])
            ->filter()
            ->unique('id')
            ->reject(fn (User $user) => $user->id === $author->id);

        foreach ($recipients as $recipient) {
            $recipient->notify(new CommentAdded($this->task, $author->name));
        }

        $this->commentBody = '';
    }

    public function deleteComment(int $commentId): void
    {
        $comment = Comment::findOrFail($commentId);

        abort_unless($comment->task_id === $this->task->id, 422);
        $this->authorize('delete', $comment);

        $comment->delete();
    }

    public function updatedUploads(): void
    {
        abort_unless(auth()->user()->can('upload attachments'), 403);

        $this->validate([
            'uploads' => ['array', 'max:5'],
            'uploads.*' => ['file', 'max:25600', 'mimes:pdf,doc,docx,png,jpg,jpeg,zip'],
        ]);

        foreach ($this->uploads as $upload) {
            /** @var TemporaryUploadedFile $upload */
            Attachment::create([
                'task_id' => $this->task->id,
                'user_id' => auth()->id(),
                'file_name' => $upload->getClientOriginalName(),
                'file_path' => $upload->store("attachments/{$this->task->id}", 'public'),
                'mime_type' => $upload->getMimeType(),
                'file_size' => $upload->getSize(),
            ]);
        }

        $this->uploads = [];
    }

    public function deleteAttachment(int $attachmentId): void
    {
        $attachment = Attachment::findOrFail($attachmentId);

        abort_unless($attachment->task_id === $this->task->id, 422);
        $this->authorize('delete', $attachment);

        Storage::disk('public')->delete($attachment->file_path);
        $attachment->delete();
    }

    public function deleteTask(): mixed
    {
        $this->authorize('delete', $this->task);

        $title = $this->task->title;
        $this->task->delete();

        session()->flash('success', "Task \"{$title}\" deleted.");

        return redirect()->route('tasks.mine');
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

    public function render(): View
    {
        $user = auth()->user();

        $this->task->loadMissing([
            'project', 'milestone', 'assignee', 'creator', 'parent',
            'subtasks.assignee', 'comments.author', 'attachments.uploader',
        ]);

        $subtasks = $this->task->subtasks->sortBy(fn (Task $s) => [$s->status === 'done', $s->id]);
        $doneCount = $subtasks->where('status', 'done')->count();

        return view('livewire.task-detail', [
            'subtasks' => $subtasks,
            'doneCount' => $doneCount,
            'subProgress' => $subtasks->isNotEmpty() ? (int) round($doneCount / $subtasks->count() * 100) : 0,
            'comments' => $this->task->comments->sortByDesc('created_at'),
            'canEdit' => Gate::allows('update', $this->task),
            'canChangeStatus' => Gate::allows('updateStatus', $this->task),
            'canAssign' => $user->can('assign tasks'),
            'canComment' => $user->can('create comments'),
            'canUpload' => $user->can('upload attachments'),
            'canDeleteTask' => Gate::allows('delete', $this->task),
            'milestones' => Milestone::where('project_id', $this->task->project_id)->orderBy('title')->get(),
            'users' => User::orderBy('name')->get(),
            'isOverdue' => $this->task->status !== 'done' && $this->task->due_date->lt(today()),
        ]);
    }
}
