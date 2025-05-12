<?php

namespace App\Actions\Persons;

use App\DTOs\Persons\PersonIndexDTO;
use App\Models\Person;
use Illuminate\Pagination\LengthAwarePaginator;
use Lorisleiva\Actions\Concerns\AsAction;

class GetPersons
{
    use AsAction;

    /**
     * Get paginated list of persons with filtering, searching, and sorting.
     *
     * @param  PersonIndexDTO  $dto
     * @return LengthAwarePaginator
     */
    public function handle(PersonIndexDTO $dto): LengthAwarePaginator
    {
        // Start with base query
        $query = Person::query()->withMovieCount();

        // Apply search if query is provided
        if ($dto->query) {
            $query->byName($dto->query);
        }

        // Apply filters
        if ($dto->types) {
            $query->whereIn('type', collect($dto->types)->map->value->toArray());
        }

        if ($dto->genders) {
            $query->whereIn('gender', collect($dto->genders)->map->value->toArray());
        }

        if ($dto->movieIds) {
            $query->whereHas('movies', function ($q) use ($dto) {
                $q->whereIn('movies.id', $dto->movieIds);
            });
        }

        // Apply age filters if provided
        if ($dto->minAge !== null || $dto->maxAge !== null) {
            if ($dto->minAge !== null) {
                $maxDate = now()->subYears($dto->minAge);
                $query->where('birthday', '<=', $maxDate);
            }

            if ($dto->maxAge !== null) {
                $minDate = now()->subYears($dto->maxAge + 1)->addDay();
                $query->where('birthday', '>=', $minDate);
            }
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
