<?php

namespace App\Policies;

use App\Models\CommentReport;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CommentReportPolicy
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
        return true; // Усі можуть бачити скарги
    }

    public function view(User $user, CommentReport $commentReport): bool
    {
        return true; // Усі можуть бачити окрему скаргу
    }

    public function create(User $user): bool
    {
        return auth()->check(); // Лише авторизовані
    }

    public function update(User $user, CommentReport $commentReport): bool
    {
        return $user->id === $commentReport->user_id; // Лише автор
    }

    public function delete(User $user, CommentReport $commentReport): bool
    {
        return $user->id === $commentReport->user_id; // Лише автор
    }
}
