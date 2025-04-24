<?php

namespace App\DTOs\Enums;

use App\DTOs\BaseDTO;
use Illuminate\Http\Request;

class EnumDTO extends BaseDTO
{
    /**
     * Create a new EnumDTO instance.
     *
     * @param string|null $locale Locale for translations
     */
    public function __construct(
        public readonly ?string $locale = null,
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
            'locale',
        ];
    }


}
