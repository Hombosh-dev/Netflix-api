<?php

namespace App\Actions\MovieTag;


use App\Models\MovieTag;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;

class ReadMovieTagAction
{
    /**
     * Повертає запис MovieTag за комбінацією movie_id та tag_id.
     *
     * @param string $movieId
     * @param string $tagId
     * @return MovieTag|null
     * @throws AuthorizationException
     */
    public function execute(string $movieId, string $tagId): ?MovieTag
    {
        Gate::authorize('view', MovieTag::class);
        return MovieTag::where('movie_id', $movieId)
            ->where('tag_id', $tagId)
            ->first();
    }
}
