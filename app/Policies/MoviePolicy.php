<?php

namespace App\Policies;

use App\Models\Movie;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class MoviePolicy
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

    public function view(User $user, Movie $movie): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return false;
    }


    public function update(User $user, Movie $movie): bool
    {
        return false;
    }

    public function delete(User $user, Movie $movie): bool
    {
        return false;
    }
}
