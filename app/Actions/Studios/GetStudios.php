<?php

namespace App\Actions\Studios;

use App\DTOs\Studios\StudioIndexDTO;
use App\Models\Studio;
use Illuminate\Pagination\LengthAwarePaginator;
use Lorisleiva\Actions\Concerns\AsAction;

class GetStudios
{
    use AsAction;

    /**
     * Get paginated list of studios with filtering, searching, and sorting.
     *
     * @param  StudioIndexDTO  $dto
     * @return LengthAwarePaginator
     */
    public function handle(StudioIndexDTO $dto): LengthAwarePaginator
    {
        // Start with base query
        $query = Studio::query()->withMovieCount();

        // Apply search if query is provided
        if ($dto->query) {
            $query->byName($dto->query);
        }

        // Apply filters
        if ($dto->hasMovies) {
            $query->withMovies();
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
            $query->orderByMovieCount($direction);
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
