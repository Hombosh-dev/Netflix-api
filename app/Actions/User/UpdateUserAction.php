<?php

namespace App\Actions\User;

use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;

class UpdateUserAction
{
    /**
     * Оновлює існуючий запис User.
     *
     * @param User $user
     * @param array{
     *     name?: string,
     *     email?: string,
     *     password?: string,
     *     role?: string,
     *     gender?: string|null,
     *     avatar?: string|null,
     *     backdrop?: string|null,
     *     description?: string|null,
     *     birthday?: string|null,
     *     allow_adult?: bool,
     *     is_auto_next?: bool,
     *     is_auto_play?: bool,
     *     is_auto_skip_intro?: bool,
     *     is_private_favorites?: bool
     * } $data
     * @return bool
     * @throws AuthorizationException
     */
    public function execute(User $user, array $data): bool
    {
        Gate::authorize('update', $user);
        return $user->update($data);
    }
}
