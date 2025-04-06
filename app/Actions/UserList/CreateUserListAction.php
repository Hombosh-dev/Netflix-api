<?php

namespace App\Actions\UserList;

use App\Models\UserList;

/**
 * Клас для створення нового запису UserList.
 *
 * @param array{
 *     user_id: string,
 *     listable_type: string,
 *     listable_id: string,
 *     type: string
 * } $data
 */
class CreateUserListAction
{
    /**
     * Виконує створення нового запису UserList.
     *
     * @param array $data
     * @return UserList
     */
    public function execute(array $data): UserList
    {
        return UserList::create($data);
    }
}
