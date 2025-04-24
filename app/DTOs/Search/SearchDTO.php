<?php

namespace App\DTOs\Search;

use App\DTOs\BaseDTO;
use Illuminate\Http\Request;

class SearchDTO extends BaseDTO
{
    /**
     * Create a new SearchDTO instance.
     *
     * @param  string  $query  The search query
     * @param  array  $types  The content types to search in
     */
    public function __construct(
        public readonly string $query,
        public readonly array $types = ['movies', 'people', 'studios', 'tags', 'selections'],
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
            'types',
        ];
    }

    /**
     * Create a new DTO instance from request.
     * Override to handle 'q' parameter mapping to 'query'.
     *
     * @param  Request  $request
     * @return static
     */
    public static function fromRequest(Request $request): static
    {
        $data = $request->all();
        $types = $request->input('types', ['movies', 'people', 'studios', 'tags', 'selections']);

        if (!is_array($types)) {
            $types = explode(',', $types);
        }

        return new static(
            query: $request->input('q', ''),
            types: $types
        );
    }
}
