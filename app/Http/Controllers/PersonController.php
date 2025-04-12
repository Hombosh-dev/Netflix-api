<?php

namespace App\Http\Controllers;

use App\Actions\Person\CreatePersonAction;
use App\Actions\Person\DeletePersonAction;
use App\Actions\Person\ReadPersonAction;
use App\Actions\Person\UpdatePersonAction;
use App\Http\Requests\People\CreatePeopleRequest;
use App\Http\Requests\People\UpdatePeopleRequest;
use App\Http\Resources\PeopleResource;
use App\Models\People;
use Illuminate\Auth\Access\AuthorizationException;

class PersonController extends Controller
{
    /**
     * Повертає колекцію всіх записів People.
     */
    public function index(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $people = People::all();
        return PeopleResource::collection($people);
    }

    /**
     * Створює новий запис People.
     * @throws AuthorizationException
     */
    public function store(CreatePeopleRequest $request, CreatePersonAction $createAction): PeopleResource
    {
        $people = $createAction->execute($request->validated());
        return new PeopleResource($people);
    }

    /**
     * Повертає дані конкретного People.
     */
    public function show(People $people, ReadPersonAction $readAction): PeopleResource
    {
        return new PeopleResource($people);
    }

    /**
     * Оновлює дані конкретного People.
     * @throws AuthorizationException
     */
    public function update(
        UpdatePeopleRequest $request,
        People $people,
        UpdatePersonAction $updateAction
    ): PeopleResource {
        $updateAction->execute($people, $request->validated());
        return new PeopleResource($people);
    }

    /**
     * Видаляє запис People.
     */
    public function destroy(People $people, DeletePersonAction $deleteAction): \Illuminate\Http\Response
    {
        $deleteAction->execute($people);
        return response()->noContent();
    }
}
