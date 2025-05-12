<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Auth\Access\Response;

class UserSubscriptionPolicy
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
        // Тільки авторизовані користувачі можуть бачити підписки
        return true;
    }

    public function view(User $user, UserSubscription $userSubscription): bool
    {
        // Користувач може переглядати тільки свої підписки
        return $user->id === $userSubscription->user_id;
    }

    public function create(User $user): bool
    {
        // Будь-який авторизований користувач може створювати підписки
        return true;
    }

    public function update(User $user, UserSubscription $userSubscription): bool
    {
        // Користувач може оновлювати тільки свої підписки
        return $user->id === $userSubscription->user_id;
    }

    public function delete(User $user, UserSubscription $userSubscription): bool
    {
        // Користувач може видаляти тільки свої підписки
        return $user->id === $userSubscription->user_id;
    }
}
