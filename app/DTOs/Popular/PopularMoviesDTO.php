<?php

namespace App\DTOs\Popular;

use App\DTOs\BaseDTO;
use Illuminate\Http\Request;

class PopularMoviesDTO extends BaseDTO
{
    /**
     * Create a new PopularMoviesDTO instance.
     *
     * @param  int  $limit  The maximum number of movies to return
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
