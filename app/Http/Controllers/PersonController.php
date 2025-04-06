<?php

namespace App\Http\Controllers;

use App\Actions\Person\CreatePersonAction;
use App\Actions\Person\DeletePersonAction;
use App\Actions\Person\ReadPersonAction;
use App\Actions\Person\UpdatePersonAction;
use App\Http\Requests\Person\CreatePersonRequest;
use App\Http\Requests\Person\UpdatePersonRequest;
use App\Http\Resources\PersonResource;
use App\Models\Person;
use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpFoundation\Response;

class PersonController extends Controller
{
    /**
     * Повертає колекцію всіх записів Person.
     */
    public function index(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $persons = Person::all();
        return PersonResource::collection($persons);
    }

    /**
     * Створює новий запис Person.
     * @throws AuthorizationException
     */
    public function store(CreatePersonRequest $request, CreatePersonAction $createAction): PersonResource
    {
        $person = $createAction->execute($request->validated());
        return new PersonResource($person);
    }

    /**
     * Повертає дані конкретного Person.
     *
     * Для пошуку за композитним ключем (movie_id та person_id) можна використовувати спеціальний метод ReadPersonAction.
     */
    public function show(string $movie_id, string $person_id, ReadPersonAction $readAction): \Illuminate\Http\JsonResponse|PersonResource
    {
        $person = $readAction->execute($movie_id, $person_id);
        if (!$person) {
            return response()->json(['message' => 'Record not found'], Response::HTTP_NOT_FOUND);
        }
        return new PersonResource($person);
    }

    /**
     * Оновлює дані конкретного Person.
     *
     * Для оновлення можна використовувати стандартне route model binding, якщо ви його налаштували.
     */
    public function update(UpdatePersonRequest $request, Person $person, UpdatePersonAction $updateAction): PersonResource
    {
        $updateAction->execute($person, $request->validated());
        return new PersonResource($person);
    }

    /**
     * Видаляє запис Person.
     * @throws AuthorizationException
     */
    public function destroy(Person $person, DeletePersonAction $deleteAction): \Illuminate\Http\Response
    {
        $deleteAction->execute($person);
        return response()->noContent();
    }
}
