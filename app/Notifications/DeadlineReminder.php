<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DeadlineReminder extends Notification
{
    use Queueable;

    public function __construct(
        public Task $task,
        public bool $isOverdue,
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
        $days = $this->task->due_date->diffInDays(today());

        return [
            'kind' => 'deadline',
            'task_id' => $this->task->id,
            'project_id' => $this->task->project_id,
            'title' => $this->isOverdue ? 'Task overdue' : 'Due tomorrow',
            'message' => $this->isOverdue
                ? "\"{$this->task->title}\" is overdue by {$days} days. Take action now."
                : "\"{$this->task->title}\" is due tomorrow ({$this->task->due_date->locale('en')->translatedFormat('d M Y')}).",
        ];
    }
}
