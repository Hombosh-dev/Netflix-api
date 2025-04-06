<?php

namespace App\Actions\MovieTag;

use App\Models\MovieTag;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;

/**
 * Клас для створення нового запису MovieTag.
 *
 * @param array{
 *     movie_id: string,
 *     tag_id: string
 * } $data
 */
class CreateMovieTagAction
{
    /**
     * Виконує створення нового запису MovieTag.
     *
     * @param array $data
     * @return MovieTag
     * @throws AuthorizationException
     */
    public function execute(array $data): MovieTag
    {
        Gate::authorize('create', MovieTag::class);
        return MovieTag::create($data);
    }
}
