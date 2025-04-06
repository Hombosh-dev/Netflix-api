<?php

namespace App\Policies;

use App\Models\Ratings;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class RatingsPolicy
{
    /**
     * Determine whether the user can view any ratings.
     */
    public function viewAny(User $user): bool
    {
        // Everyone can view ratings.
        return true;
    }

    /**
     * Determine whether the user can view the rating.
     */
    public function view(User $user, Ratings $ratings): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create a rating.
     */
    public function create(User $user): bool
    {
        // Any authenticated user can create a rating.
        return $user !== null;
    }

    /**
     * Determine whether the user can update the rating.
     */
    public function update(User $user, Ratings $ratings): bool
    {
        // Only the owner of the rating can update it.
        return $user->id === $ratings->user_id;
    }

    /**
     * Determine whether the user can delete the rating.
     */
    public function delete(User $user, Ratings $ratings): bool
    {
        // Allow deletion if the user is the owner or is an admin.
        return $user->id === $ratings->user_id || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can restore the rating.
     */
    public function restore(User $user, Ratings $ratings): bool
    {
        // Allow restore if the user is the owner or is an admin.
        return $user->id === $ratings->user_id || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can permanently delete the rating.
     */
    public function forceDelete(User $user, Ratings $ratings): bool
    {
        // Only admins can permanently delete a rating.
        return $user->hasRole('admin');
    }
}
