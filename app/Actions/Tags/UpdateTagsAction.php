<?php

namespace App\Actions\Tags;

use App\Models\Tags;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;

class UpdateTagsAction
{
    /**
     * Оновлює існуючий запис Tags.
     *
     * @param Tags $tags
     * @param array{
     *     slug?: string,
     *     name?: string,
     *     description?: string,
     *     image?: string|null,
     *     aliases?: array,
     *     is_genre?: bool,
     *     meta_title?: string|null,
     *     meta_description?: string|null,
     *     meta_image?: string|null
     * } $data
     * @return bool
     * @throws AuthorizationException
     */
    public function execute(Tags $tags, array $data): bool
    {
        Gate::authorize('update', $tags);
        return $tags->update($data);
    }
}
