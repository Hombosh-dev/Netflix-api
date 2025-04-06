<?php

namespace App\Http\Controllers;

use App\Actions\MovieNotifications\CreateMovieNotificationsAction;
use App\Actions\MovieNotifications\DeleteMovieNotificationsAction;
use App\Actions\MovieNotifications\ReadMovieNotificationsAction;
use App\Actions\MovieNotifications\UpdateMovieNotificationsAction;
use App\Http\Requests\MovieNotifications\CreateMovieNotificationsRequest;
use App\Http\Requests\MovieNotifications\UpdateMovieNotificationsRequest;
use App\Http\Resources\MovieNotificationsResource;
use App\Models\MovieNotifications;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

class MovieNotificationsController extends Controller
{
    /**
     * Повертає колекцію всіх записів MovieNotifications.
     */
    public function index(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $notifications = MovieNotifications::all();
        return MovieNotificationsResource::collection($notifications);
    }

    /**
     * Створює новий запис MovieNotifications.
     * @throws AuthorizationException
     */
    public function store(CreateMovieNotificationsRequest $request, CreateMovieNotificationsAction $createAction): MovieNotificationsResource
    {
        $notification = $createAction->execute($request->validated());
        return new MovieNotificationsResource($notification);
    }

    /**
     * Повертає дані конкретного MovieNotifications.
     */
    public function show(MovieNotifications $movieNotification, ReadMovieNotificationsAction $readAction): MovieNotificationsResource
    {
        return new MovieNotificationsResource($movieNotification);
    }

    /**
     * Оновлює дані конкретного MovieNotifications.
     * @throws AuthorizationException
     */
    public function update(UpdateMovieNotificationsRequest $request, MovieNotifications $movieNotification, UpdateMovieNotificationsAction $updateAction): MovieNotificationsResource
    {
        $updateAction->execute($movieNotification, $request->validated());
        return new MovieNotificationsResource($movieNotification);
    }

    /**
     * Видаляє запис MovieNotifications.
     * @throws AuthorizationException
     */
    public function destroy(MovieNotifications $movieNotification, DeleteMovieNotificationsAction $deleteAction): \Illuminate\Http\Response
    {
        $deleteAction->execute($movieNotification);
        return response()->noContent();
    }
}
