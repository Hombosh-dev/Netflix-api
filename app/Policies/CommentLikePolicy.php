<?php

namespace App\Policies;

use App\Models\CommentLike;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CommentLikePolicy
{
    use HandlesAuthorization;

    public function before(User $user, $ability): ?bool
    {
        if ($user->isAdmin()) {
            return true; // Адміни можуть усе
        }
        return null;
    }

    public function viewAny(User $user): bool
    {
        return true; // Усі можуть бачити лайки
    }

    public function view(User $user, CommentLike $commentLike): bool
    {
        return true; // Усі можуть бачити окремий лайк
    }

    public function create(User $user): bool
    {
        return auth()->check(); // Лише авторизовані
    }

    public function update(User $user, CommentLike $commentLike): bool
    {
        return $user->id === $commentLike->user_id; // Лише власник
    }

    public function delete(User $user, CommentLike $commentLike): bool
    {
        return $user->id === $commentLike->user_id; // Лише власник
    }
}
