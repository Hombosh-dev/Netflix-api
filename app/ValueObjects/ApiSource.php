<?php

namespace App\ValueObjects;

use App\Enums\ApiSourceName;
use JsonSerializable;

class ApiSource implements JsonSerializable
{
    public function __construct(
        public ApiSourceName $source,
        public string $id
    ) {}

    /**
     * Convert to array for JSON serialization
     */
    public function toArray(): array
    {
        return [
            'source' => $this->source->value,
            'id' => $this->id,
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
