<?php

namespace App\Actions\Studio;

use App\Models\Studio;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateStudioAction
{
    use AsAction;

    /**
     * Оновлює існуючий запис Studio.
     *
     * @param Studio $studio
     * @param array{
     *     slug?: string,
     *     meta_title?: string,
     *     meta_description?: string,
     *     meta_image?: string,
     *     name?: string,
     *     description?: string,
     *     image?: string,
     *     aliases?: string,
     *     is_genre?: bool
     * } $data
     * @return bool
     * @throws AuthorizationException
     */
    public function execute(Studio $studio, array $data): bool
    {
        Gate::authorize('update', $studio);
        return $studio->update($data);
    }
}
