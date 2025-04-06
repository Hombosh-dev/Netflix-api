<?php

namespace App\Actions\Person;

use App\Models\Person;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;

/**
 * Клас для створення нового запису Person.
 *
 * @param array{
 *     movie_id: string,
 *     person_id: string,
 *     voice_person_id?: string|null,
 *     character_name: string
 * } $data
 */
class CreatePersonAction
{
    /**
     * Виконує створення нового запису Person.
     *
     * @param array $data
     * @return Person
     * @throws AuthorizationException
     */
    public function execute(array $data): Person
    {
        Gate::authorize('create', Person::class);
        return Person::create($data);
    }
}
