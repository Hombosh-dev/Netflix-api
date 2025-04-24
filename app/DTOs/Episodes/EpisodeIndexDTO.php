<?php

namespace App\DTOs\Episodes;

use App\DTOs\BaseDTO;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class EpisodeIndexDTO extends BaseDTO
{
    /**
     * Create a new EpisodeIndexDTO instance.
     *
     * @param string|null $movieId Filter by movie ID
     * @param Carbon|null $airedAfter Filter episodes aired after this date
     * @param bool $includeFiller Whether to include filler episodes
     * @param string|null $sort Field to sort by
     * @param string $direction Sort direction (asc or desc)
     * @param int $page Current page number
     * @param int $perPage Number of items per page
     */
    public function __construct(
        public readonly ?string $movieId = null,
        public readonly ?Carbon $airedAfter = null,
        public readonly bool $includeFiller = false,
        public readonly ?string $sort = null,
        public readonly string $direction = 'asc',
        public readonly int $page = 1,
        public readonly int $perPage = 15,
    ) {}

    /**
     * Get the fields that should be used for the DTO.
     *
     * @return array
     */
    public static function fields(): array
    {
        return [
            'movie_id' => 'movieId',
            'aired_after' => 'airedAfter',
            'include_filler' => 'includeFiller',
            'sort',
            'direction',
            'page',
            'per_page' => 'perPage',
        ];
    }

    /**
     * Create a new DTO instance from request.
     *
     * @param Request $request
     * @return static
     */
    public static function fromRequest(Request $request): static
    {
        return new static(
            movieId: $request->input('movie_id'),
            airedAfter: $request->has('aired_after') ? Carbon::parse($request->input('aired_after')) : null,
            includeFiller: $request->boolean('include_filler', false),
            sort: $request->input('sort'),
            direction: $request->input('direction', 'asc'),
            page: (int) $request->input('page', 1),
            perPage: (int) $request->input('per_page', 15),
        );
    }
}
