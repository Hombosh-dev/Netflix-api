<?php

namespace App\Actions\User;

use App\Models\User;

class DeleteUserAction
{
    /**
     * Видаляє запис User.
     *
     * @param User $user
     * @return bool|null
     */
    public function execute(User $user): ?bool
    {
        return $user->delete();
    }
}
