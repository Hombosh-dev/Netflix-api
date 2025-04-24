<?php

namespace App\Http\Controllers;

use App\Actions\Studios\CreateStudio;
use App\Actions\Studios\GetStudios;
use App\Actions\Studios\UpdateStudio;
use App\DTOs\Studios\StudioIndexDTO;
use App\DTOs\Studios\StudioSearchDTO;
use App\DTOs\Studios\StudioStoreDTO;
use App\DTOs\Studios\StudioUpdateDTO;
use App\Http\Requests\Studios\StudioDeleteRequest;
use App\Http\Requests\Studios\StudioIndexRequest;
use App\Http\Requests\Studios\StudioSearchRequest;
use App\Http\Requests\Studios\StudioStoreRequest;
use App\Http\Requests\Studios\StudioUpdateRequest;
use App\Http\Resources\MovieResource;
use App\Http\Resources\StudioResource;
use App\Models\Studio;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class StudioController extends Controller
{
    /**
     * Get paginated list of studios with filtering, sorting and pagination
     *
     * @param  StudioIndexRequest  $request
     * @param  GetStudios  $action
     * @return AnonymousResourceCollection
     */
    public function index(StudioIndexRequest $request, GetStudios $action): AnonymousResourceCollection
    {
        $dto = StudioIndexDTO::fromRequest($request);
        $studios = $action->handle($dto);

        return StudioResource::collection($studios);
    }

    /**
     * Search for studios by name with filtering, sorting and pagination
     *
     * @param  string  $query
     * @param  StudioSearchRequest  $request
     * @param  GetStudios  $action
     * @return AnonymousResourceCollection
     */
    public function search(string $query, StudioSearchRequest $request, GetStudios $action): AnonymousResourceCollection
    {
        $request->merge(['q' => $query]);
        $dto = StudioIndexDTO::fromRequest($request);
        $studios = $action->handle($dto);

        return StudioResource::collection($studios);
    }

    /**
     * Get detailed information about a specific studio
     *
     * @param  Studio  $studio
     * @return StudioResource
     */
    public function show(Studio $studio): StudioResource
    {
        return new StudioResource($studio->loadCount('movies'));
    }

    /**
     * Get movies associated with a specific studio
     *
     * @param  Studio  $studio
     * @return AnonymousResourceCollection
     */
    public function movies(Studio $studio): AnonymousResourceCollection
    {
        $movies = $studio->movies()->paginate();

        return MovieResource::collection($movies);
    }

    /**
     * Store a newly created studio
     *
     * @param  StudioStoreRequest  $request
     * @param  CreateStudio  $action
     * @return StudioResource
     */
    public function store(StudioStoreRequest $request, CreateStudio $action): StudioResource
    {
        $dto = StudioStoreDTO::fromRequest($request);
        $studio = $action->handle($dto);

        return new StudioResource($studio);
    }

    /**
     * Update the specified studio
     *
     * @param  StudioUpdateRequest  $request
     * @param  Studio  $studio
     * @param  UpdateStudio  $action
     * @return StudioResource
     */
    public function update(StudioUpdateRequest $request, Studio $studio, UpdateStudio $action): StudioResource
    {
        $dto = StudioUpdateDTO::fromRequest($request);
        $studio = $action->handle($studio, $dto);

        return new StudioResource($studio);
    }

    /**
     * Remove the specified studio
     *
     * @param  StudioDeleteRequest  $request
     * @param  Studio  $studio
     * @return JsonResponse
     */
    public function destroy(StudioDeleteRequest $request, Studio $studio): JsonResponse
    {
        // Check if the studio has related movies
        if ($studio->movies()->exists()) {
            return response()->json([
                'message' => 'Cannot delete studio with associated movies. Remove associations first.',
            ], 422);
        }

        $studio->delete();

        return response()->json([
            'message' => 'Studio deleted successfully',
        ]);
    }
}
