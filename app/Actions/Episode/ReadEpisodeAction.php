<?php

namespace App\Actions\Episode;
use App\Models\Episode;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;

class ReadEpisodeAction
{
    /**
     * Повертає запис Episode за його ідентифікатором.
     *
     * @param string $id
     * @return Episode|null
     * @throws AuthorizationException
     */
    public function execute(string $id): ?Episode
    {
        Gate::authorize('view', Episode::class);
        return Episode::find($id);
    }
}
