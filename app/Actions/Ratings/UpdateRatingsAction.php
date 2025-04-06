<?php

namespace App\Actions\Ratings;

use App\Models\Ratings;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;

class UpdateRatingsAction
{
    /**
     * Оновлює існуючий запис Ratings.
     *
     * @param Ratings $ratings
     * @param array{
     *     user_id?: string,
     *     movie_id?: string,
     *     number?: int,
     *     review?: string|null
     * } $data
     * @return bool
     * @throws AuthorizationException
     */
    public function execute(Ratings $ratings, array $data): bool
    {
        Gate::authorize('update', $ratings);
        return $ratings->update($data);
    }
}
