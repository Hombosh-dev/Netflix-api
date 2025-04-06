<?php

namespace App\Http\Controllers;

use App\Actions\People\CreatePeopleAction;
use App\Actions\People\DeletePeopleAction;
use App\Actions\People\ReadPeopleAction;
use App\Actions\People\UpdatePeopleAction;
use App\Http\Requests\People\CreatePeopleRequest;
use App\Http\Requests\People\UpdatePeopleRequest;
use App\Http\Resources\PeopleResource;
use App\Models\People;
use Illuminate\Auth\Access\AuthorizationException;

class PeopleController extends Controller
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
    public function store(CreatePeopleRequest $request, CreatePeopleAction $createAction): PeopleResource
    {
        $people = $createAction->execute($request->validated());
        return new PeopleResource($people);
    }

    /**
     * Повертає дані конкретного People.
     */
    public function show(People $people, ReadPeopleAction $readAction): PeopleResource
    {
        return new PeopleResource($people);
    }

    /**
     * Оновлює дані конкретного People.
     * @throws AuthorizationException
     */
    public function update(UpdatePeopleRequest $request, People $people, UpdatePeopleAction $updateAction): PeopleResource
    {
        $updateAction->execute($people, $request->validated());
        return new PeopleResource($people);
    }

    /**
     * Видаляє запис People.
     */
    public function destroy(People $people, DeletePeopleAction $deleteAction): \Illuminate\Http\Response
    {
        $deleteAction->execute($people);
        return response()->noContent();
    }
}
