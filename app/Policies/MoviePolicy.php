<?php

namespace App\Policies;

use App\Models\Movie;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class MoviePolicy
{
    /**
     * Determine whether the user can view any movies.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the movie.
     */
    public function view(User $user, Movie $movie): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create movies.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can update the movie.
     */
    public function update(User $user, Movie $movie): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can delete the movie.
     */
    public function delete(User $user, Movie $movie): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can restore the movie.
     */
    public function restore(User $user, Movie $movie): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can permanently delete the movie.
     */
    public function forceDelete(User $user, Movie $movie): bool
    {
        return $user->hasRole('admin');
    }


}
