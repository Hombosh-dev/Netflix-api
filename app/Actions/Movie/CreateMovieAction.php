<?php

namespace App\Actions\Movie;

use App\Models\Movie;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;

/**
 * Клас для створення нового запису Movie.
 *
 * @param array{
 *     slug: string,
 *     meta_title?: string,
 *     meta_description?: string,
 *     meta_image?: string,
 *     name: string,
 *     description?: string,
 *     image?: string,
 *     aliases?: string,
 *     is_genre?: bool
 * } $data
 */
class CreateMovieAction
{
    /**
     * Виконує створення нового запису Movie.
     *
     * @param array $data
     * @return Movie
     * @throws AuthorizationException
     */
    public function execute(array $data): Movie
    {
        Gate::authorize('create', Movie::class);
        return Movie::create($data);
    }
}
