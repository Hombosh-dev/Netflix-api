<?php

namespace App\Actions\Studio;

use App\Models\Studio;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;
use Lorisleiva\Actions\Concerns\AsAction;

class DeleteStudioAction
{
    use AsAction;

    /**
     * Видаляє запис Studio.
     *
     * @param Studio $studio
     * @return bool|null
     * @throws AuthorizationException
     */
    public function execute(Studio $studio): ?bool
    {
        Gate::authorize('app.studio.destroy', $studio);
        return $studio->delete();
    }
}
