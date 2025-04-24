<?php

namespace App\DTOs\Tariffs;

use App\DTOs\BaseDTO;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class TariffUpdateDTO extends BaseDTO
{
    /**
     * Create a new TariffUpdateDTO instance.
     *
     * @param string|null $name Tariff name
     * @param string|null $description Tariff description
     * @param float|null $price Tariff price
     * @param string|null $currency Tariff currency
     * @param int|null $durationDays Tariff duration in days
     * @param array|Collection|null $features Tariff features
     * @param bool|null $isActive Whether the tariff is active
     * @param string|null $slug Tariff slug
     * @param string|null $metaTitle SEO meta title
     * @param string|null $metaDescription SEO meta description
     * @param string|null $metaImage SEO meta image
     */
    public function __construct(
        public readonly ?string $name = null,
        public readonly ?string $description = null,
        public readonly ?float $price = null,
        public readonly ?string $currency = null,
        public readonly ?int $durationDays = null,
        public readonly array|Collection|null $features = null,
        public readonly ?bool $isActive = null,
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
        $features = null;
        if ($request->has('features')) {
            $features = $request->input('features', []);
            if (is_string($features)) {
                $features = json_decode($features, true) ?? [];
            }
        }

        return new static(
            name: $request->input('name'),
            description: $request->input('description'),
            price: $request->has('price') ? (float) $request->input('price') : null,
            currency: $request->input('currency'),
            durationDays: $request->has('duration_days') ? (int) $request->input('duration_days') : null,
            features: $features,
            isActive: $request->has('is_active') ? $request->boolean('is_active') : null,
            slug: $request->input('slug'),
            metaTitle: $request->input('meta_title'),
            metaDescription: $request->input('meta_description'),
            metaImage: $request->input('meta_image'),
        );
    }
}
