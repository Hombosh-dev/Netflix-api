<?php

namespace App\DTOs\Popular;

use App\DTOs\BaseDTO;

class PopularSelectionsDTO extends BaseDTO
{
    /**
     * Create a new PopularSelectionsDTO instance.
     *
     * @param int $limit The maximum number of selections to return
     */
    public function __construct(
        public readonly int $limit = 10,
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
