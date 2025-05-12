<?php

namespace App\Http\Controllers;

use App\Actions\Search\PerformAutocomplete;
use App\Actions\Search\PerformSearch;
use App\DTOs\Search\AutocompleteDTO;
use App\DTOs\Search\SearchDTO;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\Search\SearchRequest;
use App\Http\Requests\Search\AutocompleteRequest;

class SearchController extends Controller
{
    /**
     * Search across all content types
     *
     * @param SearchRequest $request
     * @param PerformSearch $action
     * @return JsonResponse
     */
    public function search(SearchRequest $request, PerformSearch $action): JsonResponse
    {
        $dto = SearchDTO::fromRequest($request);
        $results = $action->handle($dto);

        return response()->json($results);
    }

    /**
     * Autocomplete search for quick suggestions
     *
     * @param AutocompleteRequest $request
     * @param PerformAutocomplete $action
     * @return JsonResponse
     */
    public function autocomplete(AutocompleteRequest $request, PerformAutocomplete $action): JsonResponse
    {
        $dto = AutocompleteDTO::fromRequest($request);
        $results = $action->handle($dto);

        return response()->json($results);
    }
}
