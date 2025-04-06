<?php

namespace App\Actions\Ratings;

use App\Models\Ratings;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;

class DeleteRatingsAction
{
    /**
     * Видаляє запис Ratings.
     *
     * @param Ratings $ratings
     * @return bool|null
     * @throws AuthorizationException
     */
    public function execute(Ratings $ratings): ?bool
    {
        Gate::authorize('delete-ratings');
        return $ratings->delete();
    }
}
