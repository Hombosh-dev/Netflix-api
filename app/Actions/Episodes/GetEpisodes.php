<?php

namespace App\Actions\Episodes;

use App\DTOs\Episodes\EpisodeIndexDTO;
use App\Models\Episode;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class GetEpisodes
{
    /**
     * Get episodes based on the provided DTO
     *
     * @param EpisodeIndexDTO $dto
     * @return LengthAwarePaginator
     */
    public function handle(EpisodeIndexDTO $dto): LengthAwarePaginator
    {
        $query = Episode::query();

        // Apply filters
        if ($dto->movieId) {
            $query->forMovie($dto->movieId);
        }

        if ($dto->airedAfter) {
            $query->airedAfter($dto->airedAfter);
        }

        $query->fillers($dto->includeFiller);

        // Apply sorting
        if ($dto->sort) {
            if ($dto->sort === 'number') {
                $query->orderByNumber($dto->direction);
            } else {
                $query->orderBy($dto->sort, $dto->direction);
            }
        } else {
            // Default sorting by number
            $query->orderByNumber();
        }

        // Paginate results
        return $query->paginate($dto->perPage);
    }
}
