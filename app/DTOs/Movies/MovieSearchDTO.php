<?php

namespace App\DTOs\Movies;

use App\DTOs\BaseDTO;
use App\Enums\Kind;
use App\Enums\Status;
use Illuminate\Http\Request;

class MovieSearchDTO extends BaseDTO
{
    /**
     * Create a new MovieSearchDTO instance.
     *
     * @param string $query Search query
     * @param int $page Current page number
     * @param int $perPage Number of items per page
     * @param string|null $sort Field to sort by
     * @param string $direction Sort direction (asc or desc)
     * @param Kind|null $kind Filter by movie kind
     * @param Status|null $status Filter by movie status
     * @param float|null $minScore Minimum IMDb score
     * @param float|null $maxScore Maximum IMDb score
     * @param string|null $studioId Filter by studio ID
     * @param string|null $tagId Filter by tag ID
     * @param string|null $personId Filter by person ID
     */
    public function __construct(
        public readonly string $query,
        public readonly int $page = 1,
        public readonly int $perPage = 15,
        public readonly ?string $sort = 'created_at',
        public readonly string $direction = 'desc',
        public readonly ?Kind $kind = null,
        public readonly ?Status $status = null,
        public readonly ?float $minScore = null,
        public readonly ?float $maxScore = null,
        public readonly ?string $studioId = null,
        public readonly ?string $tagId = null,
        public readonly ?string $personId = null,
    ) {
    }

    /**
     * Get the fields that should be used for the DTO.
     *
     * @return array
     */
    public static function fields(): array
    {
        return [
            'query',
            'page',
            'per_page' => 'perPage',
            'sort',
            'direction',
            'kind',
            'status',
            'min_score' => 'minScore',
            'max_score' => 'maxScore',
            'studio_id' => 'studioId',
            'tag_id' => 'tagId',
            'person_id' => 'personId',
        ];
    }

    /**
     * Create a new DTO instance from request.
     *
     * @param Request $request
     * @param string $query
     * @return static
     */
    public static function fromRequest(Request $request): static
    {
        $kind = $request->input('kind') ? Kind::from($request->input('kind')) : null;
        $status = $request->input('status') ? Status::from($request->input('status')) : null;

        return new static(
            query: $request->query,
            page: (int) $request->input('page', 1),
            perPage: (int) $request->input('per_page', 15),
            sort: $request->input('sort', 'created_at'),
            direction: $request->input('direction', 'desc'),
            kind: $kind,
            status: $status,
            minScore: $request->input('min_score') ? (float) $request->input('min_score') : null,
            maxScore: $request->input('max_score') ? (float) $request->input('max_score') : null,
            studioId: $request->input('studio_id'),
            tagId: $request->input('tag_id'),
            personId: $request->input('person_id'),
        );
    }
}
