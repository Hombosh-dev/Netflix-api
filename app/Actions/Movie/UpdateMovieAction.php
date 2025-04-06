<?php

namespace App\Actions\Movie;

use App\Models\Movie;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateMovieAction
{
    use AsAction;

    /**
     * Оновлює існуючий запис Movie.
     *
     * @param Movie $movie
     * @param array{
     *     slug?: string,
     *     meta_title?: string,
     *     meta_description?: string,
     *     meta_image?: string,
     *     name?: string,
     *     description?: string,
     *     image?: string,
     *     aliases?: string,
     *     is_genre?: bool
     * } $data
     * @return bool
     * @throws AuthorizationException
     */
    public function execute(Movie $movie, array $data): bool
    {
        Gate::authorize('update', $movie);
        return $movie->update($data);
    }
}
