<?php

namespace App\Actions\Movies;

use App\DTOs\Movies\MovieSearchDTO;
use App\Models\Movie;
use Illuminate\Pagination\LengthAwarePaginator;
use Lorisleiva\Actions\Concerns\AsAction;

class SearchMovies
{
    use AsAction;

    /**
     * Search movies with filtering, sorting and pagination.
     *
     * @param MovieSearchDTO $dto
     * @return LengthAwarePaginator
     */
    public function handle(MovieSearchDTO $dto): LengthAwarePaginator
    {
        $query = Movie::search($dto->query);

        // Apply filters to the search results
        $query = Movie::query()
            ->with('studio')
            ->whereIn('id', $query->keys());

        // Apply additional filters
        if ($dto->kind) {
            $query->where('kind', $dto->kind);
        }

        if ($dto->status) {
            $query->where('status', $dto->status);
        }

        if ($dto->minScore !== null) {
            $query->where('imdb_score', '>=', $dto->minScore);
        }

        if ($dto->maxScore !== null) {
            $query->where('imdb_score', '<=', $dto->maxScore);
        }

        if ($dto->studioId) {
            $query->where('studio_id', $dto->studioId);
        }

        if ($dto->tagId) {
            $query->whereHas('tags', function ($q) use ($dto) {
                $q->where('tags.id', $dto->tagId);
            });
        }

        if ($dto->personId) {
            $query->whereHas('persons', function ($q) use ($dto) {
                $q->where('people.id', $dto->personId);
            });
        }

        // Apply sorting
        $query->orderBy($dto->sort ?? 'created_at', $dto->direction ?? 'desc');

        // Return paginated results
        return $query->paginate(
            perPage: $dto->perPage,
            page: $dto->page
        );
    }
}
