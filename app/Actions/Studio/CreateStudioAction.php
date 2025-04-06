<?php

namespace App\Actions\Studio;

use App\Models\Studio;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateStudioAction
{
    use AsAction;

    /**
     * Створює новий запис Studio.
     *
     * @param array{
     *     slug: string,
     *     meta_title?: string,
     *     meta_description?: string,
     *     meta_image?: string,
     *     name: string,
     *     description?: string,
     *     image?: string,
     *     aliases?: string,
     *     is_genre?: bool
     * } $data
     * @return Studio
     * @throws AuthorizationException
     */
    public function execute(array $data): Studio
    {
        Gate::authorize('create', Studio::class);
        return Studio::create($data);
    }
}
