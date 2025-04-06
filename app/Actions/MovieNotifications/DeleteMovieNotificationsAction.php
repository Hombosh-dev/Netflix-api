<?php

namespace App\Actions\MovieNotifications;

use App\Models\MovieNotifications;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;

class DeleteMovieNotificationsAction
{
    /**
     * Видаляє запис MovieNotifications.
     *
     * @param MovieNotifications $movieNotification
     * @return bool|null
     * @throws AuthorizationException
     */
    public function execute(MovieNotifications $movieNotification): ?bool
    {
        Gate::authorize('delete', $movieNotification);
        return $movieNotification->delete();
    }
}
