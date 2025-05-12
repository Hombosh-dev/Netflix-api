<?php

namespace App\Policies;

use App\Enums\UserListType;
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

    public function viewAny(?User $user): bool
    {
        return true; // Усі можуть бачити списки
    }

    public function view(?User $user, UserList $userList): bool
    {
        // Якщо це не список улюблених, то дозволяємо всім
        if ($userList->type !== UserListType::FAVORITE) {
            return true;
        }

        // Якщо це список улюблених, перевіряємо налаштування приватності
        $listOwner = $userList->user;
        if ($listOwner->is_private_favorites) {
            // Якщо список приватний, то дозволяємо перегляд тільки власнику або адміну
            return $user && ($user->id === $listOwner->id || $user->isAdmin());
        }

        // Якщо список не приватний, то дозволяємо всім
        return true;
    }

    public function create(User $user): bool
    {
        return true; // Авторизовані користувачі можуть створювати списки
    }

    public function update(User $user, UserList $userList): bool
    {
        // Тільки власник списку може його оновлювати
        return $user->id === $userList->user_id;
    }

    public function delete(User $user, UserList $userList): bool
    {
        // Тільки власник списку може його видаляти
        return $user->id === $userList->user_id;
    }
}
