<?php

namespace App\Actions\People;

use App\Models\People;

class DeletePeopleAction
{
    /**
     * Видаляє запис People.
     *
     * @param People $people
     * @return bool|null
     */
    public function execute(People $people): ?bool
    {
        Gate::authorize('delete', $people);
        return $people->delete();
    }
}
