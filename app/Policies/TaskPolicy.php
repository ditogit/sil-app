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

    public function view(User $user, Task $task): bool
    {
        return $user->can('task.view') && $this->isMember($user, $task);
    }

    public function create(User $user): bool
    {
        return $user->can('task.create');
    }

    public function assign(User $user, Task $task): bool
    {
        return $user->can('task.assign') && $this->isMember($user, $task);
    }

    public function update(User $user, Task $task): bool
    {
        if (! $this->isMember($user, $task) || ! $user->can('task.update')) {
            return false;
        }

        // Team Member may only edit tasks assigned to them.
        return $user->hasRole('project-manager')
            || $task->assignee_id === $user->id;
    }

    public function updateStatus(User $user, Task $task): bool
    {
        // Admin already bypassed via Gate::before.
        if (! $this->isMember($user, $task)) {
            return false;
        }

        // Task Done hanya boleh diubah yang punya task.reopen (PM/Admin)
        if ($task->status === 'done') {
            return $user->can('task.reopen');
        }

        return $user->hasRole('project-manager')
            || ($user->can('task.update-status') && $task->assignee_id === $user->id);
    }

    public function delete(User $user, Task $task): bool
    {
        return $user->can('task.delete') && $this->isMember($user, $task);
    }
}
