<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PaymentPolicy
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

    public function view(User $user, Payment $payment): bool
    {
        // Користувач може переглядати тільки свої платежі
        return $user->id === $payment->user_id;
    }

    public function create(User $user): bool
    {
        // Будь-який авторизований користувач може створювати платежі
        return true;
    }

    public function update(User $user, Payment $payment): bool
    {
        // Користувач не може оновлювати платежі, тільки адміністратор
        return false;
    }

    public function delete(User $user, Payment $payment): bool
    {
        // Користувач не може видаляти платежі, тільки адміністратор
        return false;
    }
}
