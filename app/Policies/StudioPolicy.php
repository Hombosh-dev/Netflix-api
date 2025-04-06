<?php

namespace App\Policies;

use App\Models\Studio;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class StudioPolicy
{
    /**
     * Determine whether the user can view any studio records.
     */
    public function viewAny(User $user): bool
    {
        // All users can view studios.
        return true;
    }

    /**
     * Determine whether the user can view the studio.
     */
    public function view(User $user, Studio $studio): bool
    {
        // All users can view a studio.
        return true;
    }

    /**
     * Determine whether the user can create studios.
     */
    public function create(User $user): bool
    {
        // Only admin users can create studios.
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can update the studio.
     */
    public function update(User $user, Studio $studio): bool
    {
        // Only admin users can update studios.
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can delete the studio.
     */
    public function delete(User $user, Studio $studio): bool
    {
        // Only admin users can delete studios.
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can restore the studio.
     */
    public function restore(User $user, Studio $studio): bool
    {
        // Only admin users can restore studios.
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can permanently delete the studio.
     */
    public function forceDelete(User $user, Studio $studio): bool
    {
        // Only admin users can permanently delete studios.
        return $user->hasRole('admin');
    }
}
