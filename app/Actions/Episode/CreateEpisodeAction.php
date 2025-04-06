<?php

namespace App\Actions\Episode;

use App\Models\Episode;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;

/**
 * Клас для створення нового запису Episode.
 *
 * @param array{
 *     movie_id: string,
 *     number: int,
 *     slug: string,
 *     name: string,
 *     description?: string|null,
 *     duration?: int|null,
 *     air_date?: string|null,
 *     is_filler: bool,
 *     pictures: string,
 *     video_players: string,
 *     meta_title?: string|null,
 *     meta_description?: string|null,
 *     meta_image?: string|null
 * } $data
 */
class CreateEpisodeAction
{
    /**
     * Виконує створення нового запису Episode.
     *
     * @param array $data
     * @return Episode
     * @throws AuthorizationException
     */
    public function execute(array $data): Episode
    {
        Gate::authorize('create', Episode::class);
        return Episode::create($data);
    }
}
