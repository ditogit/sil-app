<?php

namespace App\Policies;

use App\Models\Attachment;
use App\Models\User;

class AttachmentPolicy
{
    public function delete(User $user, Attachment $attachment): bool
    {
        return $user->can('delete attachments')
            && ($attachment->user_id === $user->id || $user->can('manage all tasks'));
    }
}
