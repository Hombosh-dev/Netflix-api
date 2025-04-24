<?php

namespace App\Actions\Ratings;

use App\DTOs\Ratings\RatingIndexDTO;
use App\Models\Rating;
use Illuminate\Pagination\LengthAwarePaginator;
use Lorisleiva\Actions\Concerns\AsAction;

class GetRatings
{
    use AsAction;

    /**
     * Get paginated list of ratings with filtering, searching, and sorting.
     *
     * @param  RatingIndexDTO  $dto
     * @return LengthAwarePaginator
     */
    public function handle(RatingIndexDTO $dto): LengthAwarePaginator
    {
        // Start with base query
        $query = Rating::query()->with(['user', 'movie']);

        // Apply search if query is provided
        if ($dto->query) {
            $query->where('review', 'like', "%{$dto->query}%");
        }

        // Apply filters
        if ($dto->userId) {
            $query->forUser($dto->userId);
        }

        if ($dto->movieId) {
            $query->forMovie($dto->movieId);
        }

        if ($dto->minRating !== null && $dto->maxRating !== null) {
            $query->betweenRatings($dto->minRating, $dto->maxRating);
        } elseif ($dto->minRating !== null) {
            $query->where('number', '>=', $dto->minRating);
        } elseif ($dto->maxRating !== null) {
            $query->where('number', '<=', $dto->maxRating);
        }

        if ($dto->hasReview === true) {
            $query->withReviews();
        } elseif ($dto->hasReview === false) {
            $query->whereNull('review')->orWhere('review', '');
        }

        // Apply sorting
        $sortField = $dto->sort ?? 'created_at';
        $direction = $dto->direction ?? 'desc';
        $query->orderBy($sortField, $direction);

        // Return paginated results
        return $query->paginate(
            perPage: $dto->perPage,
            page: $dto->page
        );
    }
}
