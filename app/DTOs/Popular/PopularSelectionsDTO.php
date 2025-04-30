<?php

namespace App\DTOs\Popular;

use App\DTOs\BaseDTO;
use Illuminate\Http\Request;

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

    /**
     * Create a new DTO instance from request.
     *
     * @param Request $request
     * @return static
     */
    public static function fromRequest(Request $request): static
    {
        return new static(
            limit: (int) $request->input('limit', 10),
        );
    }
}
