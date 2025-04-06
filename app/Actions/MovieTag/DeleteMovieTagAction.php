<?php

namespace App\Actions\MovieTag;

use App\Models\MovieTag;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;

class DeleteMovieTagAction
{
    /**
     * Видаляє запис MovieTag.
     *
     * @param MovieTag $movieTag
     * @return bool|null
     * @throws AuthorizationException
     */
    public function execute(MovieTag $movieTag): ?bool
    {
        Gate::authorize('delete', $movieTag);
        return $movieTag->delete();
    }
}
