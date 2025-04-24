<?php

namespace App\Actions\Tags;

use App\DTOs\Tags\TagIndexDTO;
use App\Models\Tag;
use Illuminate\Pagination\LengthAwarePaginator;
use Lorisleiva\Actions\Concerns\AsAction;

class GetTags
{
    use AsAction;

    /**
     * Get paginated list of tags with filtering, searching, and sorting.
     *
     * @param  TagIndexDTO  $dto
     * @return LengthAwarePaginator
     */
    public function handle(TagIndexDTO $dto): LengthAwarePaginator
    {
        // Start with base query
        $query = Tag::query()->withCount('movies');

        // Apply search if query is provided
        if ($dto->query) {
            $query->search($dto->query);
        }

        // Apply filters
        if ($dto->isGenre !== null) {
            if ($dto->isGenre) {
                $query->genres();
            } else {
                $query->nonGenres();
            }
        }

        if ($dto->hasMovies) {
            $query->whereHas('movies');
        }

        if ($dto->movieIds) {
            $query->whereHas('movies', function ($q) use ($dto) {
                $q->whereIn('movies.id', $dto->movieIds);
            });
        }

        // Apply sorting
        $sortField = $dto->sort ?? 'created_at';
        $direction = $dto->direction ?? 'desc';

        if ($sortField === 'movies_count') {
            $query->orderBy('movies_count', $direction);
        } else {
            $query->orderBy($sortField, $direction);
        }

        // Return paginated results
        return $query->paginate(
            perPage: $dto->perPage,
            page: $dto->page
        );
    }
}
