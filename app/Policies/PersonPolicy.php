<?php

namespace App\Policies;

use App\Models\Person;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PersonPolicy
{
    /**
     * Determine whether the user can view any Person records.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the Person record.
     */
    public function view(User $user, Person $person): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create Person records.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can update the Person record.
     */
    public function update(User $user, Person $person): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can delete the Person record.
     */
    public function delete(User $user, Person $person): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can restore the Person record.
     */
    public function restore(User $user, Person $person): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can permanently delete the Person record.
     */
    public function forceDelete(User $user, Person $person): bool
    {
        return $user->hasRole('admin');
    }
}
