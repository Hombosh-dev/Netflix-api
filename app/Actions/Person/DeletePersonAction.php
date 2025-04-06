<?php

namespace App\Actions\Person;

use App\Models\Person;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;

class DeletePersonAction
{
    /**
     * Видаляє запис Person.
     *
     * @param Person $person
     * @return bool|null
     * @throws AuthorizationException
     */
    public function execute(Person $person): ?bool
    {
        Gate::authorize('delete', $person);
        return $person->delete();
    }
}
