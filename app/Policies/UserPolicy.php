<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Determine whether the user can view any user records.
     */
    public function viewAny(User $user): bool
    {
        // Allow viewing all users (e.g., in an admin panel) or adjust as needed.
        return true;
    }

    /**
     * Determine whether the user can view a specific user record.
     */
    public function view(User $user, User $model): bool
    {
        // Allow if the user is viewing their own record or if the user is an admin.
        return $user->id === $model->id || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can create user records.
     */
    public function create(User $user): bool
    {
        // For example, allow only admins to create new users.
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can update the user record.
     */
    public function update(User $user, User $model): bool
    {
        // Allow update if the user is updating their own profile or if they are an admin.
        return $user->id === $model->id || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can delete the user record.
     */
    public function delete(User $user, User $model): bool
    {
        // Typically, only admin users can delete user records.
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can restore the user record.
     */
    public function restore(User $user, User $model): bool
    {
        // Allow restoration only for admin users.
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can permanently delete the user record.
     */
    public function forceDelete(User $user, User $model): bool
    {
        // Only admin users may permanently delete a user record.
        return $user->hasRole('admin');
    }
}
