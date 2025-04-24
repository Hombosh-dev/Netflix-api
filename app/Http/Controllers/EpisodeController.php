<?php

namespace App\Http\Controllers;

use App\Actions\Episodes\CreateEpisode;
use App\Actions\Episodes\GetEpisodes;
use App\Actions\Episodes\UpdateEpisode;
use App\DTOs\Episodes\EpisodeIndexDTO;
use App\DTOs\Episodes\EpisodeStoreDTO;
use App\DTOs\Episodes\EpisodeUpdateDTO;
use App\Http\Requests\Episodes\EpisodeAiredAfterRequest;
use App\Http\Requests\Episodes\EpisodeDeleteRequest;
use App\Http\Requests\Episodes\EpisodeIndexRequest;
use App\Http\Requests\Episodes\EpisodeStoreRequest;
use App\Http\Requests\Episodes\EpisodeUpdateRequest;
use App\Http\Resources\EpisodeDetailResource;
use App\Http\Resources\EpisodeResource;
use App\Models\Episode;
use App\Models\Movie;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class EpisodeController extends Controller
{
    /**
     * Get paginated list of episodes with filtering, sorting and pagination
     *
     * @param  EpisodeIndexRequest  $request
     * @param  GetEpisodes  $action
     * @return AnonymousResourceCollection
     */
    public function index(EpisodeIndexRequest $request, GetEpisodes $action): AnonymousResourceCollection
    {
        $dto = EpisodeIndexDTO::fromRequest($request);
        $episodes = $action->handle($dto);

        return EpisodeResource::collection($episodes);
    }

    /**
     * Get episodes aired after a specific date
     *
     * @param  string  $date
     * @param  EpisodeAiredAfterRequest  $request
     * @param  GetEpisodes  $action
     * @return AnonymousResourceCollection
     */
    public function airedAfter(string $date, EpisodeAiredAfterRequest $request, GetEpisodes $action): AnonymousResourceCollection
    {
        // Merge aired_after date from route parameter
        $request->merge(['aired_after' => $date]);
        $dto = EpisodeIndexDTO::fromRequest($request);
        $episodes = $action->handle($dto);

        return EpisodeResource::collection($episodes);
    }

    /**
     * Get detailed information about a specific episode
     *
     * @param  Episode  $episode
     * @return EpisodeDetailResource
     */
    public function show(Episode $episode): EpisodeDetailResource
    {
        return new EpisodeDetailResource($episode->load(['movie']));
    }

    /**
     * Get episodes for a specific movie
     *
     * @param  Movie  $movie
     * @param  EpisodeIndexRequest  $request
     * @param  GetEpisodes  $action
     * @return AnonymousResourceCollection
     */
    public function forMovie(Movie $movie, EpisodeIndexRequest $request, GetEpisodes $action): AnonymousResourceCollection
    {
        // Merge movie_id into the request to use the fromRequest method
        $request->merge(['movie_id' => $movie->id]);
        $dto = EpisodeIndexDTO::fromRequest($request);
        $episodes = $action->handle($dto);

        return EpisodeResource::collection($episodes);
    }

    /**
     * Store a newly created episode
     *
     * @param  EpisodeStoreRequest  $request
     * @param  CreateEpisode  $action
     * @return EpisodeDetailResource
     */
    public function store(EpisodeStoreRequest $request, CreateEpisode $action): EpisodeDetailResource
    {
        $dto = EpisodeStoreDTO::fromRequest($request);
        $episode = $action->handle($dto);

        return new EpisodeDetailResource($episode);
    }

    /**
     * Update the specified episode
     *
     * @param  EpisodeUpdateRequest  $request
     * @param  Episode  $episode
     * @param  UpdateEpisode  $action
     * @return EpisodeDetailResource
     */
    public function update(EpisodeUpdateRequest $request, Episode $episode, UpdateEpisode $action): EpisodeDetailResource
    {
        $dto = EpisodeUpdateDTO::fromRequest($request);
        $episode = $action->handle($episode, $dto);

        return new EpisodeDetailResource($episode);
    }

    /**
     * Remove the specified episode
     *
     * @param  EpisodeDeleteRequest  $request
     * @param  Episode  $episode
     * @return JsonResponse
     */
    public function destroy(EpisodeDeleteRequest $request, Episode $episode): JsonResponse
    {
        // Check if the episode has related content (e.g., comments)
        if ($episode->comments()->exists()) {
            return response()->json([
                'message' => 'Cannot delete episode with comments. Delete comments first.',
            ], 422);
        }

        $episode->delete();

        return response()->json([
            'message' => 'Episode deleted successfully',
        ]);
    }
}
