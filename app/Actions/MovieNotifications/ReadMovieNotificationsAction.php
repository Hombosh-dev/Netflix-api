<?php

namespace App\Actions\MovieNotifications;

use App\Models\MovieNotifications;

class ReadMovieNotificationsAction
{
    /**
     * Повертає запис MovieNotifications за його ідентифікатором.
     *
     * @param string $id
     * @return MovieNotifications|null
     */
    public function execute(string $id): ?MovieNotifications
    {
        return MovieNotifications::find($id);
    }
}
