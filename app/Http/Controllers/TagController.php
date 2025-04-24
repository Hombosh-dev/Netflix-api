<?php

namespace App\Http\Controllers;

use App\Actions\Tags\CreateTag;
use App\Actions\Tags\GetTags;
use App\Actions\Tags\UpdateTag;
use App\DTOs\Tags\TagIndexDTO;
use App\DTOs\Tags\TagStoreDTO;
use App\DTOs\Tags\TagUpdateDTO;
use App\Http\Requests\Tags\TagDeleteRequest;
use App\Http\Requests\Tags\TagIndexRequest;
use App\Http\Requests\Tags\TagStoreRequest;
use App\Http\Requests\Tags\TagUpdateRequest;
use App\Http\Resources\MovieResource;
use App\Http\Resources\TagResource;
use App\Models\Tag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TagController extends Controller
{
    /**
     * Get paginated list of tags with filtering, sorting and pagination
     *
     * @param  TagIndexRequest  $request
     * @param  GetTags  $action
     * @return AnonymousResourceCollection
     */
    public function index(TagIndexRequest $request, GetTags $action): AnonymousResourceCollection
    {
        $dto = TagIndexDTO::fromRequest($request);
        $tags = $action->handle($dto);

        return TagResource::collection($tags);
    }

    /**
     * Get detailed information about a specific tag
     *
     * @param  Tag  $tag
     * @return TagResource
     */
    public function show(Tag $tag): TagResource
    {
        return new TagResource($tag->loadCount('movies'));
    }

    /**
     * Get movies associated with a specific tag
     *
     * @param  Tag  $tag
     * @return AnonymousResourceCollection
     */
    public function movies(Tag $tag): AnonymousResourceCollection
    {
        $movies = $tag->movies()->paginate();

        return MovieResource::collection($movies);
    }

    /**
     * Store a newly created tag
     *
     * @param  TagStoreRequest  $request
     * @param  CreateTag  $action
     * @return TagResource
     */
    public function store(TagStoreRequest $request, CreateTag $action): TagResource
    {
        $dto = TagStoreDTO::fromRequest($request);
        $tag = $action->handle($dto);

        return new TagResource($tag);
    }

    /**
     * Update the specified tag
     *
     * @param  TagUpdateRequest  $request
     * @param  Tag  $tag
     * @param  UpdateTag  $action
     * @return TagResource
     */
    public function update(TagUpdateRequest $request, Tag $tag, UpdateTag $action): TagResource
    {
        $dto = TagUpdateDTO::fromRequest($request);
        $tag = $action->handle($tag, $dto);

        return new TagResource($tag);
    }

    /**
     * Remove the specified tag
     *
     * @param  TagDeleteRequest  $request
     * @param  Tag  $tag
     * @return JsonResponse
     */
    public function destroy(TagDeleteRequest $request, Tag $tag): JsonResponse
    {
        // Check if the tag has related movies
        if ($tag->movies()->exists()) {
            return response()->json([
                'message' => 'Cannot delete tag with associated movies. Remove associations first.',
            ], 422);
        }

        $tag->delete();

        return response()->json([
            'message' => 'Tag deleted successfully',
        ]);
    }
}
