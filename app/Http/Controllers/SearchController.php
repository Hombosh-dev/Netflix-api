<?php

namespace App\Http\Controllers;

use App\Actions\Search\PerformAutocomplete;
use App\Actions\Search\PerformSearch;
use App\DTOs\Search\AutocompleteDTO;
use App\DTOs\Search\SearchDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Search across all content types
     *
     * @param Request $request
     * @param PerformSearch $action
     * @return JsonResponse
     */
    public function search(Request $request, PerformSearch $action): JsonResponse
    {
        $dto = SearchDTO::fromRequest($request);
        $results = $action->handle($dto);

        return response()->json($results);
    }

    /**
     * Autocomplete search for quick suggestions
     *
     * @param Request $request
     * @param PerformAutocomplete $action
     * @return JsonResponse
     */
    public function autocomplete(Request $request, PerformAutocomplete $action): JsonResponse
    {
        $dto = AutocompleteDTO::fromRequest($request);
        $results = $action->handle($dto);

        return response()->json($results);
    }
}
