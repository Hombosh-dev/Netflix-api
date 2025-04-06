<?php

namespace App\Http\Controllers;

use App\Actions\User\CreateUserAction;
use App\Actions\User\DeleteUserAction;
use App\Actions\User\ReadUserAction;
use App\Actions\User\UpdateUserAction;
use App\Http\Requests\User\CreateUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Повертає колекцію всіх записів User.
     */
    public function index(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $users = User::all();
        return UserResource::collection($users);
    }

    /**
     * Створює новий запис User.
     */
    public function store(CreateUserRequest $request, CreateUserAction $createAction): UserResource
    {
        $user = $createAction->execute($request->validated());
        return new UserResource($user);
    }

    /**
     * Повертає дані конкретного User.
     */
    public function show(User $user, ReadUserAction $readAction): UserResource
    {
        return new UserResource($user);
    }

    /**
     * Оновлює дані конкретного User.
     * @throws AuthorizationException
     */
    public function update(UpdateUserRequest $request, User $user, UpdateUserAction $updateAction): UserResource
    {
        $updateAction->execute($user, $request->validated());
        return new UserResource($user);
    }

    /**
     * Видаляє запис User.
     */
    public function destroy(User $user, DeleteUserAction $deleteAction): \Illuminate\Http\Response
    {
        $deleteAction->execute($user);
        return response()->noContent();
    }
}
