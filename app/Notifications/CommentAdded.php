<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CommentAdded extends Notification
{
    use Queueable;

    public function __construct(
        public Task $task,
        public string $authorName,
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
            'kind' => 'comment',
            'task_id' => $this->task->id,
            'project_id' => $this->task->project_id,
            'title' => 'New comment',
            'message' => "{$this->authorName} commented on \"{$this->task->title}\".",
        ];
    }
}
