<?php

namespace App\Actions\Selections;

use App\DTOs\Selections\SelectionIndexDTO;
use App\Models\Selection;
use Illuminate\Pagination\LengthAwarePaginator;
use Lorisleiva\Actions\Concerns\AsAction;

class GetSelections
{
    use AsAction;

    /**
     * Get paginated list of selections with filtering, searching, and sorting.
     *
     * @param  SelectionIndexDTO  $dto
     * @return LengthAwarePaginator
     */
    public function handle(SelectionIndexDTO $dto): LengthAwarePaginator
    {
        // Start with base query
        $query = Selection::query()->withCount(['movies', 'userLists']);

        // Apply search if query is provided
        if ($dto->query) {
            $query->where('name', 'like', "%{$dto->query}%");
        }

        // Apply filters
        if ($dto->isPublished !== null) {
            if ($dto->isPublished) {
                $query->published();
            } else {
                $query->unpublished();
            }
        }

        if ($dto->userId) {
            $query->byUser($dto->userId);
        }

        if ($dto->hasMovies) {
            $query->withMovies();
        }

        if ($dto->hasPersons) {
            $query->withPersons();
        }

        if ($dto->movieIds) {
            $query->whereHas('movies', function ($q) use ($dto) {
                $q->whereIn('movies.id', $dto->movieIds);
            });
        }

        if ($dto->personIds) {
            $query->whereHas('persons', function ($q) use ($dto) {
                $q->whereIn('people.id', $dto->personIds);
            });
        }

        // Apply sorting
        $sortField = $dto->sort ?? 'created_at';
        $direction = $dto->direction ?? 'desc';

        if ($sortField === 'movies_count') {
            $query->orderByMovieCount($direction);
        } elseif ($sortField === 'user_lists_count') {
            $query->orderBy('user_lists_count', $direction);
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
