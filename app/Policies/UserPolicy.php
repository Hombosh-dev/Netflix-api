<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    public function before(User $user, $ability): ?bool
    {
        // Якщо користувач адміністратор, дозволяємо всі дії
        if ($user->isAdmin()) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(?User $user): bool
    {
        return true; // Дозволяємо всім переглядати список користувачів
    }

    /**
     * Determine whether the user can view any models.
     */
    public function view(?User $user, User $model): bool
    {
        return true; // Дозволяємо всім переглядати профілі користувачів
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, User $model): bool
    {
        return $user->id === $model->id;
    }

    public function delete(User $user, User $model): bool
    {
        // Адміністратор не може видалити себе
        if ($user->id === $model->id) {
            return false;
        }

        // Адміністратор не може видалити іншого адміністратора
        if ($model->isAdmin()) {
            return false;
        }

        return false;
    }

    /**
     * Determine if the user can ban another user.
     *
     * @param User $user
     * @param User $model
     * @return bool
     */
    public function ban(User $user, User $model): bool
    {
        // Користувач не може заблокувати себе
        if ($user->id === $model->id) {
            return false;
        }

        // Користувач не може заблокувати адміністратора
        if ($model->isAdmin()) {
            return false;
        }

        return false;
    }
}
