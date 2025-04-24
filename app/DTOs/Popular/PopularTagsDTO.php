<?php

namespace App\DTOs\Popular;

use App\DTOs\BaseDTO;

class PopularTagsDTO extends BaseDTO
{
    /**
     * Create a new PopularTagsDTO instance.
     *
     * @param int $limit The maximum number of tags to return
     */
    public function __construct(
        public readonly int $limit = 20,
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
            'limit',
        ];
    }


}
