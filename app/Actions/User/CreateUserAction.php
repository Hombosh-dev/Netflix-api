<?php

namespace App\Actions\User;

use App\Models\User;

/**
 * Клас для створення нового запису User.
 *
 * @param array{
 *     name: string,
 *     email: string,
 *     password: string,
 *     role: string,
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
 */
class CreateUserAction
{
    /**
     * Виконує створення нового запису User.
     *
     * @param array $data
     * @return User
     */
    public function execute(array $data): User
    {
        return User::create($data);
    }
}
