<?php

namespace App\Actions\People;
use App\Models\People;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;

class ReadPeopleAction
{
    /**
     * Повертає запис People за його ідентифікатором.
     *
     * @param string $id
     * @return People|null
     * @throws AuthorizationException
     */
    public function execute(string $id): ?People
    {
        return People::find($id);
    }
}
