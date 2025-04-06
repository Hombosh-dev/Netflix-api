<?php

namespace App\Actions\MovieNotifications;
use App\Models\MovieNotifications;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;

/**
 * Клас для створення нового запису MovieNotifications.
 *
 * @param array{
 *     user_id: string,
 *     movie_id: string
 * } $data
 */
class CreateMovieNotificationsAction
{
    /**
     * Виконує створення нового запису MovieNotifications.
     *
     * @param array $data
     * @return MovieNotifications
     * @throws AuthorizationException
     */
    public function execute(array $data): MovieNotifications
    {
        Gate::authorize('create', MovieNotifications::class);
        return MovieNotifications::create($data);
    }
}
