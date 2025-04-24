<?php

namespace App\Http\Controllers;

use App\Actions\Movies\CreateMovie;
use App\Actions\Movies\GetMovies;
use App\Actions\Movies\UpdateMovie;
use App\DTOs\Movies\MovieIndexDTO;
use App\DTOs\Movies\MovieStoreDTO;
use App\DTOs\Movies\MovieUpdateDTO;
use App\Http\Requests\Movies\MovieDeleteRequest;
use App\Http\Requests\Movies\MovieIndexRequest;
use App\Http\Requests\Movies\MovieStoreRequest;
use App\Http\Requests\Movies\MovieUpdateRequest;
use App\Http\Resources\CommentResource;
use App\Http\Resources\EpisodeResource;
use App\Http\Resources\MovieDetailResource;
use App\Http\Resources\MovieResource;
use App\Http\Resources\PersonResource;
use App\Http\Resources\RatingResource;
use App\Http\Resources\TagResource;
use App\Models\Movie;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MovieController extends Controller
{
    /**
     * Get paginated list of movies with search, filtering, sorting and pagination
     *
     * @param  MovieIndexRequest  $request
     * @param  GetMovies  $action
     * @return AnonymousResourceCollection
     */
    public function index(MovieIndexRequest $request, GetMovies $action): AnonymousResourceCollection
    {
        $dto = MovieIndexDTO::fromRequest($request);
        $movies = $action->handle($dto);

        return MovieResource::collection($movies);
    }

    /**
     * Get detailed information about a specific movie
     *
     * @param  Movie  $movie
     * @return MovieDetailResource
     */
    public function show(Movie $movie): MovieDetailResource
    {
        return new MovieDetailResource($movie->load(['studio', 'tags']));
    }

    /**
     * Get episodes for a specific movie
     *
     * @param  Movie  $movie
     * @return AnonymousResourceCollection
     */
    public function episodes(Movie $movie): AnonymousResourceCollection
    {
        $episodes = $movie->episodes()->paginate();

        return EpisodeResource::collection($episodes);
    }

    /**
     * Get persons associated with a specific movie
     *
     * @param  Movie  $movie
     * @return AnonymousResourceCollection
     */
    public function persons(Movie $movie): AnonymousResourceCollection
    {
        $persons = $movie->persons()->paginate();

        return PersonResource::collection($persons);
    }

    /**
     * Get tags associated with a specific movie
     *
     * @param  Movie  $movie
     * @return AnonymousResourceCollection
     */
    public function tags(Movie $movie): AnonymousResourceCollection
    {
        $tags = $movie->tags()->paginate();

        return TagResource::collection($tags);
    }

    /**
     * Get ratings for a specific movie
     *
     * @param  Movie  $movie
     * @return AnonymousResourceCollection
     */
    public function ratings(Movie $movie): AnonymousResourceCollection
    {
        $ratings = $movie->ratings()->paginate();

        return RatingResource::collection($ratings);
    }

    /**
     * Get comments for a specific movie
     *
     * @param  Movie  $movie
     * @return AnonymousResourceCollection
     */
    public function comments(Movie $movie): AnonymousResourceCollection
    {
        $comments = $movie->comments()->paginate();

        return CommentResource::collection($comments);
    }

    /**
     * Store a newly created movie
     *
     * @param  MovieStoreRequest  $request
     * @param  CreateMovie  $action
     * @return MovieDetailResource
     */
    public function store(MovieStoreRequest $request, CreateMovie $action): MovieDetailResource
    {
        $dto = MovieStoreDTO::fromRequest($request);
        $movie = $action->handle($dto);

        return new MovieDetailResource($movie);
    }

    /**
     * Update the specified movie
     *
     * @param  MovieUpdateRequest  $request
     * @param  Movie  $movie
     * @param  UpdateMovie  $action
     * @return MovieDetailResource
     */
    public function update(MovieUpdateRequest $request, Movie $movie, UpdateMovie $action): MovieDetailResource
    {
        $dto = MovieUpdateDTO::fromRequest($request);
        $movie = $action->handle($movie, $dto);

        return new MovieDetailResource($movie);
    }

    /**
     * Update specific fields of the movie
     *
     * @param  MovieUpdateRequest  $request
     * @param  Movie  $movie
     * @param  UpdateMovie  $action
     * @return MovieDetailResource
     */
    public function updatePartial(MovieUpdateRequest $request, Movie $movie, UpdateMovie $action): MovieDetailResource
    {
        $dto = MovieUpdateDTO::fromRequest($request);
        $movie = $action->handle($movie, $dto);

        return new MovieDetailResource($movie);
    }

    /**
     * Remove the specified movie
     *
     * @param  MovieDeleteRequest  $request
     * @param  Movie  $movie
     * @return JsonResponse
     */
    public function destroy(MovieDeleteRequest $request, Movie $movie): JsonResponse
    {
        // Check if the movie has related content
        if ($movie->episodes()->exists()) {
            return response()->json([
                'message' => 'Cannot delete movie with episodes. Delete episodes first.',
            ], 422);
        }

        $movie->delete();

        return response()->json([
            'message' => 'Movie deleted successfully',
        ]);
    }
}
