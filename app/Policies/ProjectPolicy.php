<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    public function view(User $user, Project $project): bool
    {
        if (! $user->can('view projects')) {
            return false;
        }

        return $this->isMember($user, $project) || $user->can('create projects');
    }

    public function create(User $user): bool
    {
        return $user->can('create projects');
    }

    public function update(User $user, Project $project): bool
    {
        return $user->can('edit projects');
    }

    public function archive(User $user, Project $project): bool
    {
        return $user->can('archive projects');
    }

    public function manageMembers(User $user, Project $project): bool
    {
        return $user->can('manage project members');
    }

    private function isMember(User $user, Project $project): bool
    {
        return $project->members()->whereKey($user->id)->exists();
    }
}
