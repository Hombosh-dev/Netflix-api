<?php

namespace App\Policies;

use App\Models\Rating;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class RatingPolicy
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

    public function view(User $user, Rating $ratings): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Rating $ratings): bool
    {
        return $user->id === $ratings->user_id;
    }

    public function delete(User $user, Rating $ratings): bool
    {
        return $user->id === $ratings->user_id;
    }
}
