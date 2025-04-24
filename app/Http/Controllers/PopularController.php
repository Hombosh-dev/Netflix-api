<?php

namespace App\Http\Controllers;

use App\Actions\Popular\GetPopularMovies;
use App\Actions\Popular\GetPopularSeries;
use App\Actions\Popular\GetPopularPeople;
use App\Actions\Popular\GetPopularTags;
use App\Actions\Popular\GetPopularSelections;
use App\DTOs\Popular\PopularMoviesDTO;
use App\DTOs\Popular\PopularSeriesDTO;
use App\DTOs\Popular\PopularPeopleDTO;
use App\DTOs\Popular\PopularTagsDTO;
use App\DTOs\Popular\PopularSelectionsDTO;
use App\Http\Resources\MovieResource;
use App\Http\Resources\PeopleResource;
use App\Http\Resources\SelectionResource;
use App\Http\Resources\TagResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PopularController extends Controller
{
    /**
     * Get popular movies
     *
     * @param Request $request
     * @param GetPopularMovies $action
     * @return AnonymousResourceCollection
     */
    public function movies(Request $request, GetPopularMovies $action): AnonymousResourceCollection
    {
        $dto = PopularMoviesDTO::fromRequest($request);
        $movies = $action->handle($dto);

        return MovieResource::collection($movies);
    }

    /**
     * Get popular series
     *
     * @param Request $request
     * @param GetPopularSeries $action
     * @return AnonymousResourceCollection
     */
    public function series(Request $request, GetPopularSeries $action): AnonymousResourceCollection
    {
        $dto = PopularSeriesDTO::fromRequest($request);
        $series = $action->handle($dto);

        return MovieResource::collection($series);
    }

    /**
     * Get popular people
     *
     * @param Request $request
     * @param GetPopularPeople $action
     * @return AnonymousResourceCollection
     */
    public function people(Request $request, GetPopularPeople $action): AnonymousResourceCollection
    {
        $dto = PopularPeopleDTO::fromRequest($request);
        $people = $action->handle($dto);

        return PeopleResource::collection($people);
    }

    /**
     * Get popular tags
     *
     * @param Request $request
     * @param GetPopularTags $action
     * @return AnonymousResourceCollection
     */
    public function tags(Request $request, GetPopularTags $action): AnonymousResourceCollection
    {
        $dto = PopularTagsDTO::fromRequest($request);
        $tags = $action->handle($dto);

        return TagResource::collection($tags);
    }

    /**
     * Get popular selections
     *
     * @param Request $request
     * @param GetPopularSelections $action
     * @return AnonymousResourceCollection
     */
    public function selections(Request $request, GetPopularSelections $action): AnonymousResourceCollection
    {
        $dto = PopularSelectionsDTO::fromRequest($request);
        $selections = $action->handle($dto);

        return SelectionResource::collection($selections);
    }
}
