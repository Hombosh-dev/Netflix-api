<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserList;
use Illuminate\Auth\Access\Response;

class UserListPolicy
{
    /**
     * Determine whether the user can view any user list records.
     */
    public function viewAny(User $user): bool
    {
        // All authenticated users can view user list records.
        return true;
    }

    /**
     * Determine whether the user can view the user list record.
     */
    public function view(User $user, UserList $userList): bool
    {
        // All authenticated users can view a user list record.
        return true;
    }

    /**
     * Determine whether the user can create user list records.
     */
    public function create(User $user): bool
    {
        // For example, allow only admin users to create user list records.
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can update the user list record.
     */
    public function update(User $user, UserList $userList): bool
    {
        // Allow update only for admin users.
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can delete the user list record.
     */
    public function delete(User $user, UserList $userList): bool
    {
        // Allow deletion only for admin users.
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can restore the user list record.
     */
    public function restore(User $user, UserList $userList): bool
    {
        // Allow restore only for admin users.
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can permanently delete the user list record.
     */
    public function forceDelete(User $user, UserList $userList): bool
    {
        // Allow force deletion only for admin users.
        return $user->hasRole('admin');
    }
}
