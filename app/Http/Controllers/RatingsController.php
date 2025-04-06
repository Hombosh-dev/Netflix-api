<?php

namespace App\Http\Controllers;

use App\Actions\Ratings\CreateRatingsAction;
use App\Actions\Ratings\DeleteRatingsAction;
use App\Actions\Ratings\ReadRatingsAction;
use App\Actions\Ratings\UpdateRatingsAction;
use App\Http\Requests\Ratings\CreateRatingsRequest;
use App\Http\Requests\Ratings\UpdateRatingsRequest;
use App\Http\Resources\RatingsResource;
use App\Models\Ratings;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

class RatingsController extends Controller
{
    /**
     * Повертає колекцію всіх записів Ratings.
     */
    public function index(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $ratings = Ratings::all();
        return RatingsResource::collection($ratings);
    }

    /**
     * Створює новий запис Ratings.
     * @throws AuthorizationException
     */
    public function store(CreateRatingsRequest $request, CreateRatingsAction $createAction): RatingsResource
    {
        $ratings = $createAction->execute($request->validated());
        return new RatingsResource($ratings);
    }

    /**
     * Повертає дані конкретного Ratings.
     */
    public function show(Ratings $ratings, ReadRatingsAction $readAction): RatingsResource
    {
        return new RatingsResource($ratings);
    }

    /**
     * Оновлює дані конкретного Ratings.
     * @throws AuthorizationException
     */
    public function update(UpdateRatingsRequest $request, Ratings $ratings, UpdateRatingsAction $updateAction): RatingsResource
    {
        $updateAction->execute($ratings, $request->validated());
        return new RatingsResource($ratings);
    }

    /**
     * Видаляє запис Ratings.
     * @throws AuthorizationException
     */
    public function destroy(Ratings $ratings, DeleteRatingsAction $deleteAction): \Illuminate\Http\Response
    {
        $deleteAction->execute($ratings);
        return response()->noContent();
    }
}
