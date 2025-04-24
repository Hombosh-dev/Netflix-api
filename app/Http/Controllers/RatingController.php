<?php

namespace App\Http\Controllers;

use App\Actions\Ratings\CreateRating;
use App\Actions\Ratings\GetRatings;
use App\Actions\Ratings\UpdateRating;
use App\DTOs\Ratings\RatingIndexDTO;
use App\DTOs\Ratings\RatingStoreDTO;
use App\Http\Requests\Ratings\RatingDeleteRequest;
use App\Http\Requests\Ratings\RatingIndexRequest;
use App\Http\Requests\Ratings\RatingStoreRequest;
use App\Http\Requests\Ratings\RatingUpdateRequest;
use App\Http\Resources\RatingResource;
use App\Models\Movie;
use App\Models\Rating;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RatingController extends Controller
{
    /**
     * Get paginated list of ratings with filtering, sorting and pagination
     *
     * @param  RatingIndexRequest  $request
     * @param  GetRatings  $action
     * @return AnonymousResourceCollection
     */
    public function index(RatingIndexRequest $request, GetRatings $action): AnonymousResourceCollection
    {
        $dto = RatingIndexDTO::fromRequest($request);
        $ratings = $action->handle($dto);

        return RatingResource::collection($ratings);
    }

    /**
     * Store a newly created rating
     *
     * @param  RatingStoreRequest  $request
     * @param  CreateRating  $action
     * @return RatingResource
     */
    public function store(RatingStoreRequest $request, CreateRating $action): RatingResource
    {
        $dto = RatingStoreDTO::fromRequest($request);
        $rating = $action->handle($dto);

        return new RatingResource($rating->load(['user', 'movie']));
    }

    /**
     * Get detailed information about a specific rating
     *
     * @param  Rating  $rating
     * @return RatingResource
     */
    public function show(Rating $rating): RatingResource
    {
        return new RatingResource($rating->load(['user', 'movie']));
    }

    /**
     * Update the specified rating
     *
     * @param  RatingUpdateRequest  $request
     * @param  Rating  $rating
     * @param  UpdateRating  $action
     * @return RatingResource
     */
    public function update(RatingUpdateRequest $request, Rating $rating, UpdateRating $action): RatingResource
    {
        $dto = RatingStoreDTO::fromRequest($request);
        $rating = $action->handle($rating, $dto);

        return new RatingResource($rating->load(['user', 'movie']));
    }

    /**
     * Remove the specified rating
     *
     * @param  RatingDeleteRequest  $request
     * @param  Rating  $rating
     * @return JsonResponse
     */
    public function destroy(RatingDeleteRequest $request, Rating $rating): JsonResponse
    {
        $rating->delete();

        return response()->json(['message' => 'Rating deleted successfully']);
    }

    /**
     * Get ratings for a specific user
     *
     * @param  User  $user
     * @param  RatingIndexRequest  $request
     * @param  GetRatings  $action
     * @return AnonymousResourceCollection
     */
    public function forUser(User $user, RatingIndexRequest $request, GetRatings $action): AnonymousResourceCollection
    {
        $request->merge(['user_id' => $user->id]);
        $dto = RatingIndexDTO::fromRequest($request);
        $ratings = $action->handle($dto);

        return RatingResource::collection($ratings);
    }

    /**
     * Get ratings for a specific movie
     *
     * @param  Movie  $movie
     * @param  RatingIndexRequest  $request
     * @param  GetRatings  $action
     * @return AnonymousResourceCollection
     */
    public function forMovie(Movie $movie, RatingIndexRequest $request, GetRatings $action): AnonymousResourceCollection
    {
        $request->merge(['movie_id' => $movie->id]);
        $dto = RatingIndexDTO::fromRequest($request);
        $ratings = $action->handle($dto);

        return RatingResource::collection($ratings);
    }
}
