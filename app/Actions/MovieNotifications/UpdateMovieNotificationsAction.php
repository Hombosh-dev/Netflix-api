<?php

namespace App\Actions\MovieNotifications;

use App\Models\MovieNotifications;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;

class UpdateMovieNotificationsAction
{
    /**
     * Оновлює існуючий запис MovieNotifications.
     *
     * @param MovieNotifications $movieNotification
     * @param array{
     *     user_id?: string,
     *     movie_id?: string
     * } $data
     * @return bool
     * @throws AuthorizationException
     */
    public function execute(MovieNotifications $movieNotification, array $data): bool
    {
        Gate::authorize('update', $movieNotification);
        return $movieNotification->update($data);
    }
}
