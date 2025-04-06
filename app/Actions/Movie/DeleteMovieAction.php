<?php

namespace App\Actions\Movie;

use App\Models\Movie;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;
use Lorisleiva\Actions\Concerns\AsAction;

class DeleteMovieAction
{
    use AsAction;

    /**
     * Видаляє запис Movie.
     *
     * @param Movie $movie
     * @return bool|null
     * @throws AuthorizationException
     */
    public function execute(Movie $movie): ?bool
    {
        Gate::authorize('delete', $movie);
        return $movie->delete();
    }
}
