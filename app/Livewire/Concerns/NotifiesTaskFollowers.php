<?php

namespace App\Livewire\Concerns;

use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskStatusChanged;

trait NotifiesTaskFollowers
{
    private function notifyStatusChange(Task $task, User $actor, string $statusLabel): void
    {
        $recipients = collect([$task->assignee, $task->creator])
            ->filter()
            ->unique('id')
            ->reject(fn (User $user) => $user->id === $actor->id);

        foreach ($recipients as $recipient) {
            $recipient->notify(new TaskStatusChanged($task, $actor->name, $statusLabel));
        }
    }
}
