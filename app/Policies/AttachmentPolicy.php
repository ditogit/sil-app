<?php

namespace App\Policies;

use App\Models\Attachment;
use App\Models\User;

class AttachmentPolicy
{
    public function delete(User $user, Attachment $attachment): bool
    {
        // Uploader can always delete their own file.
        if ($attachment->user_id === $user->id) {
            return true;
        }

        // Only PM and Admin (via Gate::before) can delete others' attachments.
        return $user->can('attachment.delete') && $user->hasRole('project-manager');
    }
}
