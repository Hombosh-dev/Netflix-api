<?php

namespace App\DTOs\Tags;

use App\DTOs\BaseDTO;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class TagUpdateDTO extends BaseDTO
{
    /**
     * Create a new TagUpdateDTO instance.
     *
     * @param string|null $name Tag name
     * @param string|null $description Tag description
     * @param bool|null $isGenre Whether the tag is a genre
     * @param string|null $image Tag image URL
     * @param array|Collection|null $aliases Alternative names or abbreviations
     * @param string|null $slug Tag slug
     * @param string|null $metaTitle SEO meta title
     * @param string|null $metaDescription SEO meta description
     * @param string|null $metaImage SEO meta image
     */
    public function __construct(
        public readonly ?string $name = null,
        public readonly ?string $description = null,
        public readonly ?bool $isGenre = null,
        public readonly ?string $image = null,
        public readonly array|Collection|null $aliases = null,
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
            'is_genre' => 'isGenre',
            'image',
            'aliases',
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
        // Process aliases
        $aliases = null;
        if ($request->has('aliases')) {
            $aliases = $request->input('aliases', []);
            if (is_string($aliases)) {
                $aliases = json_decode($aliases, true) ?? [];
            }
        }

        // Generate slug if name is provided but slug is not
        $slug = $request->input('slug');
        if (!$slug && $request->has('name')) {
            $slug = Tag::generateSlug($request->input('name'));
        }

        return new static(
            name: $request->input('name'),
            description: $request->input('description'),
            isGenre: $request->has('is_genre') ? $request->boolean('is_genre') : null,
            image: $request->input('image'),
            aliases: $aliases,
            slug: $slug,
            metaTitle: $request->input('meta_title'),
            metaDescription: $request->input('meta_description'),
            metaImage: $request->input('meta_image'),
        );
    }
}
