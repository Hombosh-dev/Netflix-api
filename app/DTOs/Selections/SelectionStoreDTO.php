<?php

namespace App\DTOs\Selections;

use App\DTOs\BaseDTO;
use App\Models\Selection;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class SelectionStoreDTO extends BaseDTO
{
    /**
     * Create a new SelectionStoreDTO instance.
     *
     * @param string $name Selection name
     * @param string $description Selection description
     * @param string $userId User ID who created the selection
     * @param bool $isPublished Whether the selection is published
     * @param array|Collection $movieIds Movie IDs to include in the selection
     * @param array|Collection $personIds Person IDs to include in the selection
     * @param string|null $slug Selection slug
     * @param string|null $metaTitle SEO meta title
     * @param string|null $metaDescription SEO meta description
     * @param string|null $metaImage SEO meta image
     */
    public function __construct(
        public readonly string $name,
        public readonly string $description,
        public readonly string $userId,
        public readonly bool $isPublished = true,
        public readonly array|Collection $movieIds = [],
        public readonly array|Collection $personIds = [],
        public readonly ?string $slug = null,
        public readonly ?string $metaTitle = null,
        public readonly ?string $metaDescription = null,
        public readonly ?string $metaImage = null,
    ) {
    }

    /**
     * Get the fields that should be used for the DTO.
     *
     * @return array
     */
    public static function fields(): array
    {
        return [
            'name',
            'description',
            'user_id' => 'userId',
            'is_published' => 'isPublished',
            'movie_ids' => 'movieIds',
            'person_ids' => 'personIds',
            'slug',
            'meta_title' => 'metaTitle',
            'meta_description' => 'metaDescription',
            'meta_image' => 'metaImage',
        ];
    }

    /**
     * Create a new DTO instance from request.
     *
     * @param Request $request
     * @return static
     */
    public static function fromRequest(Request $request): static
    {
        // Process movie IDs
        $movieIds = self::processArrayInput($request, 'movie_ids');
        
        // Process person IDs
        $personIds = self::processArrayInput($request, 'person_ids');

        // Generate slug if not provided
        $slug = $request->input('slug');
        if (!$slug) {
            $slug = Selection::generateSlug($request->input('name'));
        }

        return new static(
            name: $request->input('name'),
            description: $request->input('description'),
            userId: $request->input('user_id', $request->user()->id),
            isPublished: $request->boolean('is_published', true),
            movieIds: $movieIds ?? [],
            personIds: $personIds ?? [],
            slug: $slug,
            metaTitle: $request->input('meta_title'),
            metaDescription: $request->input('meta_description'),
            metaImage: $request->input('meta_image'),
        );
    }

    /**
     * Process array input from request
     *
     * @param Request $request
     * @param string $key
     * @return array|null
     */
    private static function processArrayInput(Request $request, string $key): ?array
    {
        if (!$request->has($key)) {
            return [];
        }

        $input = $request->input($key);
        if (is_string($input)) {
            return explode(',', $input);
        }

        return $input;
    }
}
