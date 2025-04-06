<?php

namespace App\Actions\UserList;

use App\Models\UserList;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;

class UpdateUserListAction
{
    /**
     * Оновлює існуючий запис UserList.
     *
     * @param UserList $userList
     * @param array{
     *     user_id?: string,
     *     listable_type?: string,
     *     listable_id?: string,
     *     type?: string
     * } $data
     * @return bool
     * @throws AuthorizationException
     */
    public function execute(UserList $userList, array $data): bool
    {
        Gate::forUser($userList->user)->authorize('update', $userList);
        return $userList->update($data);
    }
}
