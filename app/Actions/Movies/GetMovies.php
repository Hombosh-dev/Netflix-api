<?php

namespace App\Actions\Movies;

use App\DTOs\Movies\MovieIndexDTO;
use App\Models\Movie;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Lorisleiva\Actions\Concerns\AsAction;

class GetMovies
{
    use AsAction;

    /**
     * Get paginated list of movies with filtering, searching, and sorting.
     *
     * @param  MovieIndexDTO  $dto
     * @return LengthAwarePaginator
     */
    public function handle(MovieIndexDTO $dto): LengthAwarePaginator
    {
        // Start with base query
        $query = Movie::query()->with('studio');

        // Apply search if query is provided
        if ($dto->query) {
            $query->search($dto->query);
        }

        // Apply filters using when() for cleaner code
        $this->applyFilters($query, $dto);

        // Apply sorting
        $query->orderBy($dto->sort ?? 'created_at', $dto->direction ?? 'desc');

        // Return paginated results
        return $query->paginate(
            perPage: $dto->perPage,
            page: $dto->page
        );
    }

    /**
     * Apply all filters to the query
     *
     * @param  Builder  $query
     * @param  MovieIndexDTO  $dto
     * @return void
     */
    private function applyFilters(Builder $query, MovieIndexDTO $dto): void
    {
        // Filter by kinds (multiple)
        \Log::info('Filtering by kinds:', [
            'kinds' => $dto->kinds ? array_map(fn($k) => $k->value, $dto->kinds) : null,
            'kinds_raw' => $dto->kinds,
        ]);

        $query->when($dto->kinds, function ($q) use ($dto) {
            // Використовуємо whereIn замість циклу з orWhere
            $kindValues = array_map(fn($kind) => $kind, $dto->kinds);
            \Log::info('Using whereIn for kinds:', ['kinds' => $kindValues]);
            $q->whereIn('kind', $kindValues);
        });

        // Filter by statuses (multiple)
        $query->when($dto->statuses, function ($q) use ($dto) {
            // Використовуємо whereIn замість циклу з orWhere
            $statusValues = array_map(fn($status) => $status, $dto->statuses);
            \Log::info('Using whereIn for statuses:', ['statuses' => $statusValues]);
            $q->whereIn('status', $statusValues);
        });

        // Filter by IMDb score range
        $query->when($dto->minScore !== null, function ($q) use ($dto) {
            $q->where('imdb_score', '>=', $dto->minScore);
        });

        $query->when($dto->maxScore !== null, function ($q) use ($dto) {
            $q->where('imdb_score', '<=', $dto->maxScore);
        });

        // Filter by studios (multiple)
        $query->when($dto->studioIds, function ($q) use ($dto) {
            $q->whereIn('studio_id', $dto->studioIds);
        });

        // Filter by tags (multiple)
        $query->when($dto->tagIds, function ($q) use ($dto) {
            $q->whereHas('tags', function ($subQuery) use ($dto) {
                $subQuery->whereIn('tags.id', $dto->tagIds);
            });
        });

        // Filter by persons (multiple)
        $query->when($dto->personIds, function ($q) use ($dto) {
            $q->whereHas('persons', function ($subQuery) use ($dto) {
                $subQuery->whereIn('people.id', $dto->personIds);
            });
        });

        // Filter by release year range
        $query->when($dto->minYear !== null, function ($q) use ($dto) {
            $q->whereYear('first_air_date', '>=', $dto->minYear);
        });

        $query->when($dto->maxYear !== null, function ($q) use ($dto) {
            $q->whereYear('first_air_date', '<=', $dto->maxYear);
        });

        // Filter by countries (multiple)
        $query->when($dto->countries, function ($q) use ($dto) {
            $q->where(function ($subQuery) use ($dto) {
                foreach ($dto->countries as $country) {
                    $subQuery->orWhereJsonContains('countries', $country);
                }
            });
        });

        // Filter by duration range
        $query->when($dto->minDuration !== null, function ($q) use ($dto) {
            $q->where('duration', '>=', $dto->minDuration);
        });

        $query->when($dto->maxDuration !== null, function ($q) use ($dto) {
            $q->where('duration', '<=', $dto->maxDuration);
        });

        // Filter by episodes count range
        $query->when($dto->minEpisodesCount !== null, function ($q) use ($dto) {
            $q->where('episodes_count', '>=', $dto->minEpisodesCount);
        });

        $query->when($dto->maxEpisodesCount !== null, function ($q) use ($dto) {
            $q->where('episodes_count', '<=', $dto->maxEpisodesCount);
        });
    }
}
