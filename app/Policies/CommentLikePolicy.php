<?php

namespace App\Policies;

use App\Models\CommentLike;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CommentLikePolicy
{
    /**
     * Determine whether the user can view any comment likes.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the comment like.
     */
    public function view(User $user, CommentLike $like): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create comment likes.
     */
    public function create(User $user): bool
    {
        return $user !== null;
    }

    /**
     * Determine whether the user can update the comment like.
     */
    public function update(User $user, CommentLike $like): bool
    {
        return $user->id === $like->user_id;
    }

    /**
     * Determine whether the user can delete the comment like.
     */
    public function delete(User $user, CommentLike $like): bool
    {
        return $user->id === $like->user_id || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can restore the comment like.
     */
    public function restore(User $user, CommentLike $like): bool
    {
        return $user->id === $like->user_id || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can permanently delete the comment like.
     */
    public function forceDelete(User $user, CommentLike $like): bool
    {
        return $user->hasRole('admin');
    }
}
