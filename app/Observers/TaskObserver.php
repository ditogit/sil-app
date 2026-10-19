<?php

namespace App\Observers;

use App\Models\Task;

class TaskObserver
{
    public function saved(Task $task): void
    {
        $this->recalculate($task);
    }

    public function deleted(Task $task): void
    {
        $this->recalculate($task);
    }

    public function restored(Task $task): void
    {
        $this->recalculate($task);
    }

    public function forceDeleted(Task $task): void
    {
        $this->recalculate($task);
    }

    private function recalculate(Task $task): void
    {
        $project = $task->project()->withCount(['tasks', 'tasks as done_tasks_count' => fn ($query) => $query->where('status', 'done')])->first();

        if (! $project) {
            return;
        }

        $project->progress = $project->tasks_count > 0
            ? (int) round($project->done_tasks_count / $project->tasks_count * 100)
            : 0;

        $project->saveQuietly();
    }
}
