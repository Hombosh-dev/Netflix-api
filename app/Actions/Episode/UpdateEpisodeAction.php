<?php

namespace App\Actions\Episode;

use App\Models\Episode;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;

class UpdateEpisodeAction
{
    /**
     * Оновлює існуючий запис Episode.
     *
     * @param Episode $episode
     * @param array{
     *     movie_id?: string,
     *     number?: int,
     *     slug?: string,
     *     name?: string,
     *     description?: string|null,
     *     duration?: int|null,
     *     air_date?: string|null,
     *     is_filler?: bool,
     *     pictures?: string,
     *     video_players?: string,
     *     meta_title?: string|null,
     *     meta_description?: string|null,
     *     meta_image?: string|null
     * } $data
     * @return bool
     * @throws AuthorizationException
     */
    public function execute(Episode $episode, array $data): bool
    {
        Gate::authorize('update', $episode);
        return $episode->update($data);
    }
}
