<?php

namespace App\DTOs\Stats;

use App\DTOs\BaseDTO;
use Illuminate\Http\Request;

class StatsDTO extends BaseDTO
{
    /**
     * Create a new StatsDTO instance.
     *
     * @param int|null $days Number of days to look back for "new" items
     */
    public function __construct(
        public readonly ?int $days = 7,
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
            'days',
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
            days: (int) $request->input('days', 7),
        );
    }
}
