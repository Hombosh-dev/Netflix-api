<?php

namespace App\Actions\Movie;

use App\Models\Movie;
use Illuminate\Database\Eloquent\Collection;

class SearchMovieAction
{
    public function execute(string $query): Collection
    {
        return Movie::query()
            ->search($query)
            ->get();
    }
}
