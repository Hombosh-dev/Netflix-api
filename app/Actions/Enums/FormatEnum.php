<?php

namespace App\Actions\Enums;

use App\DTOs\Enums\EnumDTO;
use Illuminate\Support\Facades\App;
use Lorisleiva\Actions\Concerns\AsAction;

class FormatEnum
{
    use AsAction;

    /**
     * Format enum with SEO data
     *
     * @param string $enumType
     * @param object $enum
     * @param EnumDTO|null $dto
     * @return array
     */
    public function handle(string $enumType, object $enum, ?EnumDTO $dto = null): array
    {
        // Set locale if provided in DTO
        if ($dto && $dto->locale) {
            App::setLocale($dto->locale);
        }

        $locale = App::getLocale();
        $translationKey = "enums.{$enumType}.{$enum->value}";
        $metaTitleKey = "enums.{$enumType}.meta_title.{$enum->value}";
        $metaDescriptionKey = "enums.{$enumType}.meta_description.{$enum->value}";
        $metaImageKey = "enums.{$enumType}.meta_image.{$enum->value}";

        return [
            'value' => $enum->value,
            'label' => __($translationKey),
            'color' => method_exists($enum, 'getColor') ? $enum->getColor() : null,
            'icon' => method_exists($enum, 'getIcon') ? $enum->getIcon() : null,
            'seo' => [
                'meta_title' => __($metaTitleKey),
                'meta_description' => __($metaDescriptionKey),
                'meta_image' => __($metaImageKey),
            ],
        ];
    }
}
