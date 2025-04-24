<?php

namespace App\DTOs\Search;

use App\DTOs\BaseDTO;
use Illuminate\Http\Request;

class AutocompleteDTO extends BaseDTO
{
    /**
     * Create a new AutocompleteDTO instance.
     *
     * @param  string  $query  The search query
     */
    public function __construct(
        public readonly string $query,
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
            'q' => 'query',
        ];
    }


}
