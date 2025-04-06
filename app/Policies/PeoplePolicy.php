<?php

namespace App\Policies;

use App\Models\People;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PeoplePolicy
{
    /**
     * Determine whether the user can view any People records.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the People record.
     */
    public function view(User $user, People $people): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create People records.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can update the People record.
     */
    public function update(User $user, People $people): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can delete the People record.
     */
    public function delete(User $user, People $people): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can restore the People record.
     */
    public function restore(User $user, People $people): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can permanently delete the People record.
     */
    public function forceDelete(User $user, People $people): bool
    {
        return $user->hasRole('admin');
    }
}
