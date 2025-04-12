<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserList;
use Illuminate\Auth\Access\Response;

class UserListPolicy
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

    public function view(User $user, UserList $userList): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, UserList $userList): bool
    {
        return false;
    }

    public function delete(User $user, UserList $userList): bool
    {
        return false;
    }
}
