<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Notifications\DeadlineReminder;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('notifications:send-deadline-reminders')]
#[Description('Send due-date reminders (D-1) and overdue notices to assignees')]
class SendDeadlineReminders extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $tasks = Task::with('assignee')
            ->open()
            ->whereNotNull('assignee_id')
            ->where(fn ($query) => $query
                ->whereDate('due_date', today()->addDay())
                ->orWhere(fn ($q) => $q->where('due_date', '<', today())))
            ->get();

        $sent = 0;

        foreach ($tasks as $task) {
            $recentlyReminded = $task->assignee->notifications()
                ->where('type', DeadlineReminder::class)
                ->where('created_at', '>=', now()->subHours(20))
                ->get()
                ->contains(fn ($notification) => (int) ($notification->data['task_id'] ?? 0) === $task->id);

            if ($recentlyReminded) {
                continue;
            }

            $task->assignee->notify(new DeadlineReminder($task, $task->due_date->lt(today())));
            $sent++;
        }

        $this->info("Reminders sent: {$sent}.");

        return self::SUCCESS;
    }
}
