<?php

namespace App\DTOs\Popular;

use App\DTOs\BaseDTO;
use Illuminate\Http\Request;

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

    /**
     * Create a new DTO instance from request.
     *
     * @param Request $request
     * @return static
     */
    public static function fromRequest(Request $request): static
    {
        return new static(
            limit: (int) $request->input('limit', 20),
        );
    }
}
