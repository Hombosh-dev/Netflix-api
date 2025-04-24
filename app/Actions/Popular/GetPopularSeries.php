<?php

namespace App\Actions\Popular;

use App\DTOs\Popular\PopularSeriesDTO;
use App\Enums\Kind;
use App\Models\Movie;
use Illuminate\Database\Eloquent\Collection;
use Lorisleiva\Actions\Concerns\AsAction;

class GetPopularSeries
{
    use AsAction;

    /**
     * Get popular series.
     *
     * @param PopularSeriesDTO $dto
     * @return Collection
     */
    public function handle(PopularSeriesDTO $dto): Collection
    {
        return Movie::where('kind', Kind::TV_SERIES)
            ->orWhere('kind', Kind::ANIMATED_SERIES)
            ->orderBy('imdb_score', 'desc')
            ->take($dto->limit)
            ->get();
    }
}
