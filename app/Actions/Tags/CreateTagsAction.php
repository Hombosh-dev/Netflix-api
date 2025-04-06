<?php

namespace App\Actions\Tags;
use App\Models\Tags;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;

/**
 * Клас для створення нового запису Tags.
 *
 * @param array{
 *     slug: string,
 *     name: string,
 *     description: string,
 *     image?: string|null,
 *     aliases?: array,
 *     is_genre: bool,
 *     meta_title?: string|null,
 *     meta_description?: string|null,
 *     meta_image?: string|null
 * } $data
 */
class CreateTagsAction
{
    /**
     * Виконує створення нового запису Tags.
     *
     * @param array $data
     * @return Tags
     * @throws AuthorizationException
     */
    public function execute(array $data): Tags
    {
        Gate::authorize('create', Tags::class);
        return Tags::create($data);
    }
}
