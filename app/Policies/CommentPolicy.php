<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;
use App\Utils\RoleHelper;

class CommentPolicy
{
    /**
     * Determine whether the user can update the comment.
     */
    public function update(User $user, Comment $comment): bool
    {
        return $user->id === $comment->user_id;
    }

    /**
     * Determine whether the user can delete the comment.
     */
    public function delete(User $user, Comment $comment): bool
    {
        $allowed_role = $user->hasRole([
            RoleHelper::DEFAULT_ROLES['ADMIN'],
            RoleHelper::DEFAULT_ROLES['DEVELOPER'],
            RoleHelper::DEFAULT_ROLES['MODERATOR']
        ]);
        return $user->id === $comment->user_id || $allowed_role;
    }
}
