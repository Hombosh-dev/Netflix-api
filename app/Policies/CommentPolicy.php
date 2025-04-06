<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CommentPolicy
{
    /**
     * Determine whether the user can view any comments.
     */
    public function viewAny(User $user): bool
    {
        // All authenticated users can view comments
        return true;
    }

    /**
     * Determine whether the user can view the comment.
     */
    public function view(User $user, Comment $comment): bool
    {
        // Everyone can view a comment.
        return true;
    }

    /**
     * Determine whether the user can create comments.
     */
    public function create(User $user): bool
    {
        // Any authenticated user can create a comment
        return true;
    }

    /**
     * Determine whether the user can update the comment.
     */
    public function update(User $user, Comment $comment): bool
    {
        // Allow update if the user owns the comment or is an admin
        return $user->id === $comment->user_id || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can delete the comment.
     */
    public function delete(User $user, Comment $comment): bool
    {
        // Allow deletion if the user owns the comment or is an admin
        return $user->id === $comment->user_id || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can restore the comment.
     */
    public function restore(User $user, Comment $comment): bool
    {
        // Allow restore if the user owns the comment or is an admin
        return $user->id === $comment->user_id || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can permanently delete the comment.
     */
    public function forceDelete(User $user, Comment $comment): bool
    {
        // Allow force delete only for admins
        return $user->hasRole('admin');
    }
}
