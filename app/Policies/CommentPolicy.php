<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CommentPolicy
{
    use HandlesAuthorization;

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
        return true; // Усі можуть бачити коментарі
    }

    public function view(User $user, Comment $comment): bool
    {
        return true; // Усі можуть бачити окремий коментар
    }

    public function create(User $user): bool
    {
        return auth()->check(); // Лише авторизовані
    }

    public function update(User $user, Comment $comment): bool
    {
        return $user->id === $comment->user_id; // Лише автор
    }

    public function delete(User $user, Comment $comment): bool
    {
        return $user->id === $comment->user_id; // Лише автор
    }

    public function restore(User $user, Comment $comment): bool
    {
        return false; // Тільки адміни через before
    }

    public function forceDelete(User $user, Comment $comment): bool
    {
        return false; // Тільки адміни через before
    }
}
