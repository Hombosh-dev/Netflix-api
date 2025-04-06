<?php

namespace App\Actions\Ratings;

use App\Models\Ratings;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;

/**
 * Клас для створення нового запису Ratings.
 *
 * @param array{
 *     user_id: string,
 *     movie_id: string,
 *     number: int,
 *     review?: string|null
 * } $data
 */
class CreateRatingsAction
{
    /**
     * Виконує створення нового запису Ratings.
     *
     * @param array $data
     * @return Ratings
     * @throws AuthorizationException
     */
    public function execute(array $data): Ratings
    {
        Gate::authorize('create', Ratings::class);
        return Ratings::create($data);
    }
}
