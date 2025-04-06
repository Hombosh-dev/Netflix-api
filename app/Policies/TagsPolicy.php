<?php

namespace App\Policies;

use App\Models\Tags;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TagsPolicy
{
    /**
     * Determine whether the user can view any tags.
     */
    public function viewAny(User $user): bool
    {
        // All users can view tags.
        return true;
    }

    /**
     * Determine whether the user can view the tag.
     */
    public function view(User $user, Tags $tag): bool
    {
        // All users can view a tag.
        return true;
    }

    /**
     * Determine whether the user can create tags.
     */
    public function create(User $user): bool
    {
        // Only admin users can create tags.
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can update the tag.
     */
    public function update(User $user, Tags $tag): bool
    {
        // Only admin users can update tags.
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can delete the tag.
     */
    public function delete(User $user, Tags $tag): bool
    {
        // Only admin users can delete tags.
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can restore the tag.
     */
    public function restore(User $user, Tags $tag): bool
    {
        // Only admin users can restore tags.
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can permanently delete the tag.
     */
    public function forceDelete(User $user, Tags $tag): bool
    {
        // Only admin users can permanently delete tags.
        return $user->hasRole('admin');
    }
}
