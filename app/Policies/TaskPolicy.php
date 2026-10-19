<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    private function isMember(User $user, Task $task): bool
    {
        return $task->project->members()->whereKey($user->id)->exists();
    }

    public function create(User $user): bool
    {
        return $user->can('create tasks');
    }

    public function view(User $user, Task $task): bool
    {
        return $user->can('view tasks') && $this->isMember($user, $task);
    }

    public function update(User $user, Task $task): bool
    {
        return $this->isMember($user, $task)
            && $user->can('edit tasks')
            && ($task->created_by === $user->id
                || $task->assignee_id === $user->id
                || $user->can('manage all tasks'));
    }

    public function updateStatus(User $user, Task $task): bool
    {
        if (! $this->isMember($user, $task) || ! $user->can('update task status')) {
            return false;
        }

        // A done task can only be reopened by holders of reopen tasks.
        if ($task->status === 'done') {
            return $user->can('reopen tasks');
        }

        // Only the assignee or a PM (manage all tasks).
        return $task->assignee_id === $user->id || $user->can('manage all tasks');
    }

    public function delete(User $user, Task $task): bool
    {
        return $user->can('delete tasks');
    }
}
