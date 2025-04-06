<?php

namespace App\Http\Controllers;

use App\Actions\UserList\CreateUserListAction;
use App\Actions\UserList\DeleteUserListAction;
use App\Actions\UserList\ReadUserListAction;
use App\Actions\UserList\UpdateUserListAction;
use App\Http\Requests\UserList\CreateUserListRequest;
use App\Http\Requests\UserList\UpdateUserListRequest;
use App\Http\Resources\UserListResource;
use App\Models\UserList;
use Illuminate\Auth\Access\AuthorizationException;


class UserListController extends Controller
{
    /**
     * Повертає колекцію всіх записів UserList.
     */
    public function index(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $userLists = UserList::all();
        return UserListResource::collection($userLists);
    }

    /**
     * Створює новий запис UserList.
     */
    public function store(CreateUserListRequest $request, CreateUserListAction $createAction): UserListResource
    {
        $userList = $createAction->execute($request->validated());
        return new UserListResource($userList);
    }

    /**
     * Повертає дані конкретного UserList.
     */
    public function show(UserList $userList, ReadUserListAction $readAction): UserListResource
    {
        return new UserListResource($userList);
    }

    /**
     * Оновлює дані конкретного UserList.
     * @throws AuthorizationException
     */
    public function update(UpdateUserListRequest $request, UserList $userList, UpdateUserListAction $updateAction): UserListResource
    {
        $updateAction->execute($userList, $request->validated());
        return new UserListResource($userList);
    }

    /**
     * Видаляє запис UserList.
     * @throws AuthorizationException
     */
    public function destroy(UserList $userList, DeleteUserListAction $deleteAction): \Illuminate\Http\Response
    {
        $deleteAction->execute($userList);
        return response()->noContent();
    }
}
