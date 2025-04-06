<?php

namespace App\Actions\User;

use App\Models\User;

class ReadUserAction
{
    /**
     * Повертає запис User за його ідентифікатором.
     *
     * @param string $id
     * @return User|null
     */
    public function execute(string $id): ?User
    {
        return User::find($id);
    }
}
