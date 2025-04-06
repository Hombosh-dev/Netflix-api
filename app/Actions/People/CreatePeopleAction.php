<?php

namespace App\Actions\People;

use App\Models\People;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;

/**
 * Клас для створення нового запису People.
 *
 * @param array{
 *     slug: string,
 *     name: string,
 *     original_name?: string|null,
 *     image?: string|null,
 *     description?: string|null,
 *     birthday?: string|null,
 *     birthplace?: string|null,
 *     meta_title?: string|null,
 *     meta_description?: string|null,
 *     meta_image?: string|null,
 *     type: string,
 *     gender?: string|null
 * } $data
 */
class CreatePeopleAction
{
    /**
     * Виконує створення нового запису People.
     *
     * @param array $data
     * @return People
     * @throws AuthorizationException
     */
    public function execute(array $data): People
    {
        Gate::authorize('create', People::class);
        return People::create($data);
    }
}
