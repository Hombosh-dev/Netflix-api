<?php

namespace App\ValueObjects;

use App\Enums\AttachmentType;
use JsonSerializable;

class Attachment implements JsonSerializable
{
    public function __construct(
        public AttachmentType $type,
        public string $url,
        public string $title,
        public int $duration
    ) {}

    /**
     * Convert to array for JSON serialization
     */
    public function toArray(): array
    {
        return [
            'type' => $this->type->value,
            'url' => $this->url,
            'title' => $this->title,
            'duration' => $this->duration,
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
