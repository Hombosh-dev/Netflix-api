<?php

namespace App\Actions\Selection;

use App\Models\Selection;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;

class DeleteSelectionAction
{
    /**
     * Видаляє запис Selection.
     *
     * @param Selection $selection
     * @return bool|null
     * @throws AuthorizationException
     */
    public function execute(Selection $selection): ?bool
    {
        Gate::authorize('delete', $selection);
        return $selection->delete();
    }
}
