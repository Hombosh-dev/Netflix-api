<?php

namespace App\Actions\Person;

use App\Models\Person;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;

class ReadPersonAction
{
    /**
     * Повертає запис Person за комбінацією movie_id та person_id.
     *
     * @param string $movieId
     * @param string $personId
     * @return Person|null
     */
    public function execute(string $movieId, string $personId): ?Person
    {
        return Person::where('movie_id', $movieId)
            ->where('person_id', $personId)
            ->first();
    }
}
