<?php

namespace App\Http\Controllers;

use App\Actions\Selection\CreateSelectionAction;
use App\Actions\Selection\DeleteSelectionAction;
use App\Actions\Selection\ReadSelectionAction;
use App\Actions\Selection\UpdateSelectionAction;
use App\Http\Requests\Selection\CreateSelectionRequest;
use App\Http\Requests\Selection\UpdateSelectionRequest;
use App\Http\Resources\SelectionResource;
use App\Models\Selection;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

class SelectionController extends Controller
{
    /**
     * Повертає колекцію всіх записів Selection.
     */
    public function index()
    {
        $selections = Selection::all();
        return SelectionResource::collection($selections);
    }

    /**
     * Створює новий запис Selection.
     */
    public function store(CreateSelectionRequest $request, CreateSelectionAction $createAction)
    {
        $selection = $createAction->execute($request->validated());
        return new SelectionResource($selection);
    }

    /**
     * Повертає дані конкретного Selection.
     */
    public function show(Selection $selection, ReadSelectionAction $readAction): SelectionResource
    {
        return new SelectionResource($selection);
    }

    /**
     * Оновлює дані конкретного Selection.
     * @throws AuthorizationException
     */
    public function update(UpdateSelectionRequest $request, Selection $selection, UpdateSelectionAction $updateAction): SelectionResource
    {
        $updateAction->execute($selection, $request->validated());
        return new SelectionResource($selection);
    }

    /**
     * Видаляє запис Selection.
     * @throws AuthorizationException
     */
    public function destroy(Selection $selection, DeleteSelectionAction $deleteAction): \Illuminate\Http\Response
    {
        $deleteAction->execute($selection);
        return response()->noContent();
    }
}
