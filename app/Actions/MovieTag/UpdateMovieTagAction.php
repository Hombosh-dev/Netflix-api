<?php

namespace App\Actions\MovieTag;

use App\Models\MovieTag;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;

class UpdateMovieTagAction
{
    /**
     * Оновлює існуючий запис MovieTag.
     *
     * @param MovieTag $movieTag
     * @param array{
     *     movie_id?: string,
     *     tag_id?: string
     * } $data
     * @return bool
     * @throws AuthorizationException
     */
    public function execute(MovieTag $movieTag, array $data): bool
    {
        Gate::authorize('update', $movieTag);
        return $movieTag->update($data);
    }
}
