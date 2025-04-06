<?php

namespace App\Actions\People;
use App\Models\People;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;

class UpdatePeopleAction
{
    /**
     * Оновлює існуючий запис People.
     *
     * @param People $people
     * @param array{
     *     slug?: string,
     *     name?: string,
     *     original_name?: string|null,
     *     image?: string|null,
     *     description?: string|null,
     *     birthday?: string|null,
     *     birthplace?: string|null,
     *     meta_title?: string|null,
     *     meta_description?: string|null,
     *     meta_image?: string|null,
     *     type?: string,
     *     gender?: string|null
     * } $data
     * @return bool
     * @throws AuthorizationException
     */
    public function execute(People $people, array $data): bool
    {
        Gate::authorize('update', $people);
        return $people->update($data);
    }
}
