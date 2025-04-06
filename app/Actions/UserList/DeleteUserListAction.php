<?php

namespace App\Actions\UserList;

use App\Models\UserList;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;

class DeleteUserListAction
{
    /**
     * Видаляє запис UserList.
     *
     * @param UserList $userList
     * @return bool|null
     * @throws AuthorizationException
     */
    public function execute(UserList $userList): ?bool
    {
        Gate::forUser($userList->user)->authorize('delete', $userList);
        return $userList->delete();
    }
}
