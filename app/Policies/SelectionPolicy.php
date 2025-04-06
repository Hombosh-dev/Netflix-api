<?php

namespace App\Policies;

use App\Models\Selection;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SelectionPolicy
{
    /**
     * Determine whether the user can view any selections.
     */
    public function viewAny(User $user): bool
    {
        // Everyone can view selections.
        return true;
    }

    /**
     * Determine whether the user can view the selection.
     */
    public function view(User $user, Selection $selection): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create a selection.
     */
    public function create(User $user): bool
    {
        // For example, allow only admins to create a selection.
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can update the selection.
     */
    public function update(User $user, Selection $selection): bool
    {
        // Only admin users can update selections.
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can delete the selection.
     */
    public function delete(User $user, Selection $selection): bool
    {
        // Only admin users can delete selections.
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can restore the selection.
     */
    public function restore(User $user, Selection $selection): bool
    {
        // Only admin users can restore selections.
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can permanently delete the selection.
     */
    public function forceDelete(User $user, Selection $selection): bool
    {
        // Only admin users can permanently delete selections.
        return $user->hasRole('admin');
    }
}
