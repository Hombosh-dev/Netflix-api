<?php

namespace App\Policies;

use App\Models\People;
use App\Models\Person;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PersonPolicy
{
    public function before(User $user, $ability): ?bool
    {
        // Якщо користувач адміністратор, дозволяємо всі дії
        if ($user->isAdmin()) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return true;
    }


    public function view(User $user, Person $person): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, Person $person): bool
    {
        return false;
    }

    public function delete(User $user, Person $person): bool
    {
        return false;
    }
}
