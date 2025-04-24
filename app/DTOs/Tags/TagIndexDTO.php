<?php

namespace App\DTOs\Tags;

use App\DTOs\BaseDTO;
use Illuminate\Http\Request;

class TagIndexDTO extends BaseDTO
{
    /**
     * Create a new TagIndexDTO instance.
     *
     * @param string|null $query Search query
     * @param int $page Current page number
     * @param int $perPage Number of items per page
     * @param string|null $sort Field to sort by
     * @param string $direction Sort direction (asc or desc)
     * @param bool|null $isGenre Filter by genre status
     * @param bool|null $hasMovies Filter tags that have movies
     * @param array|null $movieIds Filter by movie IDs
     */
    public function __construct(
        public readonly ?string $query = null,
        public readonly int $page = 1,
        public readonly int $perPage = 15,
        public readonly ?string $sort = 'created_at',
        public readonly string $direction = 'desc',
        public readonly ?bool $isGenre = null,
        public readonly ?bool $hasMovies = null,
        public readonly ?array $movieIds = null,
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
            'is_genre' => 'isGenre',
            'has_movies' => 'hasMovies',
            'movie_ids' => 'movieIds',
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
        // Process movie IDs array
        $movieIds = self::processArrayInput($request, 'movie_ids');

        return new static(
            query: $request->input('q'),
            page: (int) $request->input('page', 1),
            perPage: (int) $request->input('per_page', 15),
            sort: $request->input('sort', 'created_at'),
            direction: $request->input('direction', 'desc'),
            isGenre: $request->has('is_genre') ? (bool) $request->input('is_genre') : null,
            hasMovies: $request->has('has_movies') ? (bool) $request->input('has_movies') : null,
            movieIds: $movieIds,
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
