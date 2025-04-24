<?php

namespace App\DTOs\Persons;

use App\DTOs\BaseDTO;
use App\Enums\Gender;
use App\Enums\PersonType;
use Illuminate\Http\Request;

class PersonIndexDTO extends BaseDTO
{
    /**
     * Create a new PersonIndexDTO instance.
     *
     * @param string|null $query Search query
     * @param int $page Current page number
     * @param int $perPage Number of items per page
     * @param string|null $sort Field to sort by
     * @param string $direction Sort direction (asc or desc)
     * @param array|null $types Filter by person types
     * @param array|null $genders Filter by genders
     * @param array|null $movieIds Filter by movie IDs
     * @param int|null $minAge Minimum age
     * @param int|null $maxAge Maximum age
     */
    public function __construct(
        public readonly ?string $query = null,
        public readonly int $page = 1,
        public readonly int $perPage = 15,
        public readonly ?string $sort = 'created_at',
        public readonly string $direction = 'desc',
        public readonly ?array $types = null,
        public readonly ?array $genders = null,
        public readonly ?array $movieIds = null,
        public readonly ?int $minAge = null,
        public readonly ?int $maxAge = null,
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
            'types',
            'genders',
            'movie_ids' => 'movieIds',
            'min_age' => 'minAge',
            'max_age' => 'maxAge',
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
        // Process types array
        $types = null;
        if ($request->has('types')) {
            $typesInput = $request->input('types');
            if (is_string($typesInput)) {
                $typesInput = explode(',', $typesInput);
            }
            $types = collect($typesInput)->map(fn($t) => PersonType::from($t))->toArray();
        }

        // Process genders array
        $genders = null;
        if ($request->has('genders')) {
            $gendersInput = $request->input('genders');
            if (is_string($gendersInput)) {
                $gendersInput = explode(',', $gendersInput);
            }
            $genders = collect($gendersInput)->map(fn($g) => Gender::from($g))->toArray();
        }

        // Process movie IDs array
        $movieIds = self::processArrayInput($request, 'movie_ids');

        return new static(
            query: $request->input('q'),
            page: (int) $request->input('page', 1),
            perPage: (int) $request->input('per_page', 15),
            sort: $request->input('sort', 'created_at'),
            direction: $request->input('direction', 'desc'),
            types: $types,
            genders: $genders,
            movieIds: $movieIds,
            minAge: $request->input('min_age') ? (int) $request->input('min_age') : null,
            maxAge: $request->input('max_age') ? (int) $request->input('max_age') : null,
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
