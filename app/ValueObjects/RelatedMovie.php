<?php

namespace App\ValueObjects;

use App\Enums\MovieRelateType;
use JsonSerializable;

class RelatedMovie implements JsonSerializable
{
    public function __construct(
        public string $movie_id,
        public MovieRelateType $type
    ) {}

    /**
     * Convert to array for JSON serialization
     */
    public function toArray(): array
    {
        return [
            'movie_id' => $this->movie_id,
            'type' => $this->type->value,
        ];
    }

    /**
     * Custom JSON serialization
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
