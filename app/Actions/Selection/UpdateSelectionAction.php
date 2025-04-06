<?php

namespace App\Actions\Selection;

use App\Models\Selection;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;

class UpdateSelectionAction
{
    /**
     * Оновлює існуючий запис Selection.
     *
     * @param Selection $selection
     * @param array{
     *     user_id?: string,
     *     slug?: string,
     *     name?: string,
     *     description?: string|null,
     *     meta_title?: string|null,
     *     meta_description?: string|null,
     *     meta_image?: string|null
     * } $data
     * @return bool
     * @throws AuthorizationException
     */
    public function execute(Selection $selection, array $data): bool
    {
        Gate::authorize('update', $selection);
        return $selection->update($data);
    }
}
