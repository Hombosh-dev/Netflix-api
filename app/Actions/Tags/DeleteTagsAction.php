<?php

namespace App\Actions\Tags;

use App\Models\Tags;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;

class DeleteTagsAction
{
    /**
     * Видаляє запис Tags.
     *
     * @param Tags $tags
     * @return bool|null
     * @throws AuthorizationException
     */
    public function execute(Tags $tags): ?bool
    {
        Gate::authorize('delete', $tags);
        return $tags->delete();
    }
}
