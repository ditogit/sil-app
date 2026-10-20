<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    public function view(User $user, Project $project): bool
    {
        if (! $user->can('project.view')) {
            return false;
        }

        return $this->isMember($user, $project) || $user->can('project.create');
    }

    public function create(User $user): bool
    {
        return $user->can('project.create');
    }

    public function update(User $user, Project $project): bool
    {
        return $user->can('project.update') && $this->isMember($user, $project);
    }

    public function archive(User $user, Project $project): bool
    {
        return $user->can('project.archive') && $this->isMember($user, $project);
    }

    public function manageMembers(User $user, Project $project): bool
    {
        return $user->can('project.manage-members') && $this->isMember($user, $project);
    }

    private function isMember(User $user, Project $project): bool
    {
        return $project->members()->whereKey($user->id)->exists();
    }
}
