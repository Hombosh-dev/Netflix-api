<?php

namespace App\DTOs\Popular;

use App\DTOs\BaseDTO;

class PopularSeriesDTO extends BaseDTO
{
    /**
     * Create a new PopularSeriesDTO instance.
     *
     * @param int $limit The maximum number of series to return
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
