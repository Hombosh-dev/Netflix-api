<?php

namespace App\DTOs\Popular;

use App\DTOs\BaseDTO;

class PopularPeopleDTO extends BaseDTO
{
    /**
     * Create a new PopularPeopleDTO instance.
     *
     * @param int $limit The maximum number of people to return
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
