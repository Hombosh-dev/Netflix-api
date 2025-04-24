<?php

namespace App\Actions\Popular;

use App\DTOs\Popular\PopularMoviesDTO;
use App\Enums\Kind;
use App\Models\Movie;
use Illuminate\Database\Eloquent\Collection;
use Lorisleiva\Actions\Concerns\AsAction;

class GetPopularMovies
{
    use AsAction;

    /**
     * Get popular movies.
     *
     * @param PopularMoviesDTO $dto
     * @return Collection
     */
    public function handle(PopularMoviesDTO $dto): Collection
    {
        return Movie::where('kind', Kind::MOVIE)
            ->orWhere('kind', Kind::ANIMATED_MOVIE)
            ->orderBy('imdb_score', 'desc')
            ->take($dto->limit)
            ->get();
    }
}
