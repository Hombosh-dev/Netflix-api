<?php

namespace App\Policies;

use App\Models\Episode;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class EpisodePolicy
{
    /**
     * Determine whether the user can view any episodes.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the episode.
     */
    public function view(User $user, Episode $episode): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create an episode.
     */
    public function create(User $user): bool
    {
        // Only admin can create episodes
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can update the episode.
     */
    public function update(User $user, Episode $episode): bool
    {
        // Only admin can update episodes
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can delete the episode.
     */
    public function delete(User $user, Episode $episode): bool
    {
        // Only admin can delete episodes
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can restore the episode.
     */
    public function restore(User $user, Episode $episode): bool
    {
        // Only admin can restore episodes
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can permanently delete the episode.
     */
    public function forceDelete(User $user, Episode $episode): bool
    {
        // Only admin can force delete episodes
        return $user->hasRole('admin');
    }
}
