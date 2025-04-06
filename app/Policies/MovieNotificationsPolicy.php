<?php

namespace App\Policies;

use App\Models\MovieNotifications;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class MovieNotificationsPolicy
{
    /**
     * Determine whether the user can view any movie notifications.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the movie notification.
     */
    public function view(User $user, MovieNotifications $notification): bool
    {
        return $user->id === $notification->user_id;
    }

    /**
     * Determine whether the user can create movie notifications.
     */
    public function create(User $user): bool
    {
        // Any authenticated user may create a notification.
        return $user !== null;
    }

    /**
     * Determine whether the user can update the movie notification.
     */
    public function update(User $user, MovieNotifications $notification): bool
    {
        // Allow update if the user is the owner or an admin.
        return $user->id === $notification->user_id || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can delete the movie notification.
     */
    public function delete(User $user, MovieNotifications $notification): bool
    {
        // Allow deletion if the user is the owner or an admin.
        return $user->id === $notification->user_id || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can restore the movie notification.
     */
    public function restore(User $user, MovieNotifications $notification): bool
    {
        return $user->id === $notification->user_id || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can permanently delete the movie notification.
     */
    public function forceDelete(User $user, MovieNotifications $notification): bool
    {
        return $user->hasRole('admin');
    }
}
