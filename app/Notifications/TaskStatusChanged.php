<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TaskStatusChanged extends Notification
{
    use Queueable;

    public function __construct(
        public Task $task,
        public string $actorName,
        public string $statusLabel,
    ) {
        //
    }

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'kind' => 'status',
            'task_id' => $this->task->id,
            'project_id' => $this->task->project_id,
            'title' => 'Task status changed',
            'message' => "{$this->actorName} changed \"{$this->task->title}\" to {$this->statusLabel}.",
        ];
    }
}
