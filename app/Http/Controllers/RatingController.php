<?php

namespace App\Http\Controllers;

use App\Actions\Rating\CreateRatingAction;
use App\Actions\Rating\DeleteRatingAction;
use App\Actions\Rating\ReadRatingAction;
use App\Actions\Rating\UpdateRatingAction;
use App\Http\Requests\Ratings\CreateRatingsRequest;
use App\Http\Requests\Ratings\UpdateRatingsRequest;
use App\Http\Resources\RatingsResource;
use App\Models\Rating;
use Illuminate\Auth\Access\AuthorizationException;

class RatingController extends Controller
{
    /**
     * Повертає колекцію всіх записів Ratings.
     */
    public function index(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $ratings = Rating::all();
        return RatingsResource::collection($ratings);
    }

    /**
     * Створює новий запис Ratings.
     * @throws AuthorizationException
     */
    public function store(CreateRatingsRequest $request, CreateRatingAction $createAction): RatingsResource
    {
        $ratings = $createAction->execute($request->validated());
        return new RatingsResource($ratings);
    }

    /**
     * Повертає дані конкретного Ratings.
     */
    public function show(Rating $ratings, ReadRatingAction $readAction): RatingsResource
    {
        return new RatingsResource($ratings);
    }

    /**
     * Оновлює дані конкретного Ratings.
     * @throws AuthorizationException
     */
    public function update(
        UpdateRatingsRequest $request,
        Rating $ratings,
        UpdateRatingAction $updateAction
    ): RatingsResource {
        $updateAction->execute($ratings, $request->validated());
        return new RatingsResource($ratings);
    }

    /**
     * Видаляє запис Ratings.
     * @throws AuthorizationException
     */
    public function destroy(Rating $ratings, DeleteRatingAction $deleteAction): \Illuminate\Http\Response
    {
        $deleteAction->execute($ratings);
        return response()->noContent();
    }
}
