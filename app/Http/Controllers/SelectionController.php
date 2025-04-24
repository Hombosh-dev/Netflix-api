<?php

namespace App\Http\Controllers;

use App\Actions\Selections\CreateSelection;
use App\Actions\Selections\GetSelections;
use App\Actions\Selections\UpdateSelection;
use App\DTOs\Selections\SelectionIndexDTO;
use App\DTOs\Selections\SelectionStoreDTO;
use App\DTOs\Selections\SelectionUpdateDTO;
use App\Http\Requests\Selections\SelectionDeleteRequest;
use App\Http\Requests\Selections\SelectionIndexRequest;
use App\Http\Requests\Selections\SelectionStoreRequest;
use App\Http\Requests\Selections\SelectionUpdateRequest;
use App\Http\Resources\MovieResource;
use App\Http\Resources\PersonResource;
use App\Http\Resources\SelectionResource;
use App\Models\Selection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SelectionController extends Controller
{
    /**
     * Get paginated list of selections with filtering, sorting and pagination
     *
     * @param  SelectionIndexRequest  $request
     * @param  GetSelections  $action
     * @return AnonymousResourceCollection
     */
    public function index(SelectionIndexRequest $request, GetSelections $action): AnonymousResourceCollection
    {
        $dto = SelectionIndexDTO::fromRequest($request);
        $selections = $action->handle($dto);

        return SelectionResource::collection($selections);
    }

    /**
     * Get detailed information about a specific selection
     *
     * @param  Selection  $selection
     * @return SelectionResource
     */
    public function show(Selection $selection): SelectionResource
    {
        return new SelectionResource($selection->load(['user', 'movies', 'persons'])->loadCount(['movies', 'userLists']));
    }

    /**
     * Get movies associated with a specific selection
     *
     * @param  Selection  $selection
     * @return AnonymousResourceCollection
     */
    public function movies(Selection $selection): AnonymousResourceCollection
    {
        $movies = $selection->movies()->paginate();

        return MovieResource::collection($movies);
    }

    /**
     * Get persons associated with a specific selection
     *
     * @param  Selection  $selection
     * @return AnonymousResourceCollection
     */
    public function persons(Selection $selection): AnonymousResourceCollection
    {
        $persons = $selection->persons()->paginate();

        return PersonResource::collection($persons);
    }

    /**
     * Store a newly created selection
     *
     * @param  SelectionStoreRequest  $request
     * @param  CreateSelection  $action
     * @return SelectionResource
     */
    public function store(SelectionStoreRequest $request, CreateSelection $action): SelectionResource
    {
        $dto = SelectionStoreDTO::fromRequest($request);
        $selection = $action->handle($dto);

        return new SelectionResource($selection);
    }

    /**
     * Update the specified selection
     *
     * @param  SelectionUpdateRequest  $request
     * @param  Selection  $selection
     * @param  UpdateSelection  $action
     * @return SelectionResource
     */
    public function update(SelectionUpdateRequest $request, Selection $selection, UpdateSelection $action): SelectionResource
    {
        $dto = SelectionUpdateDTO::fromRequest($request);
        $selection = $action->handle($selection, $dto);

        return new SelectionResource($selection);
    }

    /**
     * Remove the specified selection
     *
     * @param  SelectionDeleteRequest  $request
     * @param  Selection  $selection
     * @return JsonResponse
     */
    public function destroy(SelectionDeleteRequest $request, Selection $selection): JsonResponse
    {
        $selection->movies()->detach();
        $selection->persons()->detach();
        $selection->delete();

        return response()->json([
            'message' => 'Selection deleted successfully',
        ]);
    }
}
