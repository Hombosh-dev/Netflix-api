<?php

namespace App\Actions\UserList;

use App\Models\UserList;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;

class ReadUserListAction
{
    /**
     * Повертає запис UserList за його ідентифікатором.
     *
     * @param string $id
     * @return UserList|null
     * @throws AuthorizationException
     */
    public function execute(string $id): ?UserList
    {
        Gate::authorize('view', UserList::class);
        return UserList::find($id);
    }
}
