<?php

namespace App\Actions\Episode;

use App\Models\Episode;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;

class DeleteEpisodeAction
{
    /**
     * Видаляє запис Episode.
     *
     * @param Episode $episode
     * @return bool|null
     * @throws AuthorizationException
     */
    public function execute(Episode $episode): ?bool
    {
        Gate::authorize('delete', $episode);
        return $episode->delete();
    }
}
