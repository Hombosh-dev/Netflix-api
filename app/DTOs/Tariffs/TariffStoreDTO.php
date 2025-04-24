<?php

namespace App\DTOs\Tariffs;

use App\DTOs\BaseDTO;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class TariffStoreDTO extends BaseDTO
{
    /**
     * Create a new TariffStoreDTO instance.
     *
     * @param string $name Tariff name
     * @param string $description Tariff description
     * @param float $price Tariff price
     * @param string $currency Tariff currency
     * @param int $durationDays Tariff duration in days
     * @param array|Collection $features Tariff features
     * @param bool $isActive Whether the tariff is active
     * @param string $slug Tariff slug
     * @param string|null $metaTitle SEO meta title
     * @param string|null $metaDescription SEO meta description
     * @param string|null $metaImage SEO meta image
     */
    public function __construct(
        public readonly string $name,
        public readonly string $description,
        public readonly float $price,
        public readonly string $currency,
        public readonly int $durationDays,
        public readonly array|Collection $features,
        public readonly bool $isActive,
        public readonly string $slug,
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
            'price',
            'currency',
            'duration_days' => 'durationDays',
            'features',
            'is_active' => 'isActive',
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
        $features = $request->input('features', []);
        if (is_string($features)) {
            $features = json_decode($features, true) ?? [];
        }

        return new static(
            name: $request->input('name'),
            description: $request->input('description'),
            price: (float) $request->input('price'),
            currency: $request->input('currency'),
            durationDays: (int) $request->input('duration_days'),
            features: $features,
            isActive: $request->boolean('is_active', true),
            slug: $request->input('slug'),
            metaTitle: $request->input('meta_title'),
            metaDescription: $request->input('meta_description'),
            metaImage: $request->input('meta_image'),
        );
    }
}
