<?php

namespace App\DTOs\Persons;

use App\DTOs\BaseDTO;
use App\Enums\Gender;
use App\Enums\PersonType;
use App\Models\Person;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PersonStoreDTO extends BaseDTO
{
    /**
     * Create a new PersonStoreDTO instance.
     *
     * @param string $name Person name
     * @param PersonType $type Person type
     * @param string|null $originalName Original name in native language
     * @param Gender|null $gender Person gender
     * @param string|null $image Person photo URL
     * @param string|null $description Person biography
     * @param Carbon|null $birthday Person birthday
     * @param string|null $birthplace Person birthplace
     * @param string|null $slug Person slug
     * @param string|null $metaTitle SEO meta title
     * @param string|null $metaDescription SEO meta description
     * @param string|null $metaImage SEO meta image
     */
    public function __construct(
        public readonly string $name,
        public readonly PersonType $type,
        public readonly ?string $originalName = null,
        public readonly ?Gender $gender = null,
        public readonly ?string $image = null,
        public readonly ?string $description = null,
        public readonly ?Carbon $birthday = null,
        public readonly ?string $birthplace = null,
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
            'type',
            'original_name' => 'originalName',
            'gender',
            'image',
            'description',
            'birthday',
            'birthplace',
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
        // Generate slug if not provided
        $slug = $request->input('slug');
        if (!$slug) {
            $slug = Person::generateSlug($request->input('name'));
        }

        return new static(
            name: $request->input('name'),
            type: PersonType::from($request->input('type')),
            originalName: $request->input('original_name'),
            gender: $request->has('gender') ? Gender::from($request->input('gender')) : null,
            image: $request->input('image'),
            description: $request->input('description'),
            birthday: $request->has('birthday') ? Carbon::parse($request->input('birthday')) : null,
            birthplace: $request->input('birthplace'),
            slug: $slug,
            metaTitle: $request->input('meta_title'),
            metaDescription: $request->input('meta_description'),
            metaImage: $request->input('meta_image'),
        );
    }
}
