<?php

namespace App\Policies;

use App\Models\Episode;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class EpisodePolicy
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

    public function view(User $user, Episode $episode): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        // Only admin can create episodes
        return false;
    }

    public function update(User $user, Episode $episode): bool
    {
        return false;
    }

    public function delete(User $user, Episode $episode): bool
    {
        return false;
    }
}
