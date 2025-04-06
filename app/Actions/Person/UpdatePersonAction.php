<?php

namespace App\Actions\Person;

use App\Models\Person;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;

class UpdatePersonAction
{
    /**
     * Оновлює існуючий запис Person.
     *
     * @param Person $person
     * @param array{
     *     movie_id?: string,
     *     person_id?: string,
     *     voice_person_id?: string|null,
     *     character_name?: string
     * } $data
     * @return bool
     * @throws AuthorizationException
     */
    public function execute(Person $person, array $data): bool
    {
        Gate::authorize('update', $person);
        return $person->update($data);
    }
}
