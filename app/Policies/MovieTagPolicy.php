<?php

namespace App\Policies;

use App\Models\MovieTag;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class MovieTagPolicy
{
    /**
     * Determine whether the user can view any movie tag records.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the movie tag record.
     */
    public function view(User $user, MovieTag $movieTag): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create movie tag records.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can update the movie tag record.
     */
    public function update(User $user, MovieTag $movieTag): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can delete the movie tag record.
     */
    public function delete(User $user, MovieTag $movieTag): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can restore the movie tag record.
     */
    public function restore(User $user, MovieTag $movieTag): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can permanently delete the movie tag record.
     */
    public function forceDelete(User $user, MovieTag $movieTag): bool
    {
        return $user->hasRole('admin');
    }
}
