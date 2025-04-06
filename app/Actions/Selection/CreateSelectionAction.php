<?php

namespace App\Actions\Selection;
use App\Models\Selection;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;

/**
 * Клас для створення нового запису Selection.
 *
 * @param array{
 *     user_id: string,
 *     slug: string,
 *     name: string,
 *     description?: string|null,
 *     meta_title?: string|null,
 *     meta_description?: string|null,
 *     meta_image?: string|null
 * } $data
 */
class CreateSelectionAction
{
    /**
     * Виконує створення нового запису Selection.
     *
     * @param array $data
     * @return Selection
     * @throws AuthorizationException
     */
    public function execute(array $data): Selection
    {
        Gate::authorize('create', Selection::class);
        return Selection::create($data);
    }
}
