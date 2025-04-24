<?php

namespace App\DTOs\Movies;

use App\DTOs\BaseDTO;
use App\Enums\Kind;
use App\Enums\Status;
use Illuminate\Http\Request;

class MovieIndexDTO extends BaseDTO
{
    /**
     * Create a new MovieIndexDTO instance.
     *
     * @param string|null $query Search query
     * @param int $page Current page number
     * @param int $perPage Number of items per page
     * @param string|null $sort Field to sort by
     * @param string $direction Sort direction (asc or desc)
     * @param array|null $kinds Filter by movie kinds
     * @param array|null $statuses Filter by movie statuses
     * @param float|null $minScore Minimum IMDb score
     * @param float|null $maxScore Maximum IMDb score
     * @param array|null $studioIds Filter by studio IDs
     * @param array|null $tagIds Filter by tag IDs
     * @param array|null $personIds Filter by person IDs
     * @param int|null $minYear Minimum release year
     * @param int|null $maxYear Maximum release year
     * @param array|null $countries Filter by countries
     * @param int|null $minDuration Minimum duration in minutes
     * @param int|null $maxDuration Maximum duration in minutes
     * @param int|null $minEpisodesCount Minimum episodes count
     * @param int|null $maxEpisodesCount Maximum episodes count
     */
    public function __construct(
        public readonly ?string $query = null,
        public readonly int $page = 1,
        public readonly int $perPage = 15,
        public readonly ?string $sort = 'created_at',
        public readonly string $direction = 'desc',
        public readonly ?array $kinds = null,
        public readonly ?array $statuses = null,
        public readonly ?float $minScore = null,
        public readonly ?float $maxScore = null,
        public readonly ?array $studioIds = null,
        public readonly ?array $tagIds = null,
        public readonly ?array $personIds = null,
        public readonly ?int $minYear = null,
        public readonly ?int $maxYear = null,
        public readonly ?array $countries = null,
        public readonly ?int $minDuration = null,
        public readonly ?int $maxDuration = null,
        public readonly ?int $minEpisodesCount = null,
        public readonly ?int $maxEpisodesCount = null,
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
            'q' => 'query',
            'page',
            'per_page' => 'perPage',
            'sort',
            'direction',
            'kinds',
            'statuses',
            'min_score' => 'minScore',
            'max_score' => 'maxScore',
            'studio_ids' => 'studioIds',
            'tag_ids' => 'tagIds',
            'person_ids' => 'personIds',
            'min_year' => 'minYear',
            'max_year' => 'maxYear',
            'countries',
            'min_duration' => 'minDuration',
            'max_duration' => 'maxDuration',
            'min_episodes_count' => 'minEpisodesCount',
            'max_episodes_count' => 'maxEpisodesCount',
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
        // Process kinds array
        $kinds = null;
        if ($request->has('kinds')) {
            $kindsInput = $request->input('kinds');
            if (is_string($kindsInput)) {
                $kindsInput = explode(',', $kindsInput);
            }
            $kinds = collect($kindsInput)->map(fn($k) => Kind::from($k))->toArray();
        }

        // Process statuses array
        $statuses = null;
        if ($request->has('statuses')) {
            $statusesInput = $request->input('statuses');
            if (is_string($statusesInput)) {
                $statusesInput = explode(',', $statusesInput);
            }
            $statuses = collect($statusesInput)->map(fn($s) => Status::from($s))->toArray();
        }

        // Process array inputs
        $studioIds = self::processArrayInput($request, 'studio_ids');
        $tagIds = self::processArrayInput($request, 'tag_ids');
        $personIds = self::processArrayInput($request, 'person_ids');
        $countries = self::processArrayInput($request, 'countries');

        return new static(
            query: $request->input('q'),
            page: (int) $request->input('page', 1),
            perPage: (int) $request->input('per_page', 15),
            sort: $request->input('sort', 'created_at'),
            direction: $request->input('direction', 'desc'),
            kinds: $kinds,
            statuses: $statuses,
            minScore: $request->input('min_score') ? (float) $request->input('min_score') : null,
            maxScore: $request->input('max_score') ? (float) $request->input('max_score') : null,
            studioIds: $studioIds,
            tagIds: $tagIds,
            personIds: $personIds,
            minYear: $request->input('min_year') ? (int) $request->input('min_year') : null,
            maxYear: $request->input('max_year') ? (int) $request->input('max_year') : null,
            countries: $countries,
            minDuration: $request->input('min_duration') ? (int) $request->input('min_duration') : null,
            maxDuration: $request->input('max_duration') ? (int) $request->input('max_duration') : null,
            minEpisodesCount: $request->input('min_episodes_count') ? (int) $request->input('min_episodes_count') : null,
            maxEpisodesCount: $request->input('max_episodes_count') ? (int) $request->input('max_episodes_count') : null,
        );
    }

    /**
     * Process array input from request
     *
     * @param Request $request
     * @param string $key
     * @return array|null
     */
    private static function processArrayInput(Request $request, string $key): ?array
    {
        if (!$request->has($key)) {
            return null;
        }

        $input = $request->input($key);
        if (is_string($input)) {
            return explode(',', $input);
        }

        return $input;
    }
}
