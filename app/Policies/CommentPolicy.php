<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;

class CommentPolicy
{
    public function create(User $user, Comment $comment): bool
    {
        if (! $user->can('comment.create')) {
            return false;
        }

        // User must be a member of the project that owns this comment's task.
        return $comment->task->project->members()->whereKey($user->id)->exists();
    }

    public function delete(User $user, Comment $comment): bool
    {
        return $comment->user_id === $user->id || $user->can('comment.delete-any');
    }
}
