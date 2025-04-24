<?php

namespace App\ValueObjects;

use App\Enums\VideoPlayerName;
use App\Enums\VideoQuality;
use JsonSerializable;

class VideoPlayer implements JsonSerializable
{
    public function __construct(
        public VideoPlayerName $name,
        public string $url,
        public string $file_url,
        public string $dubbing,
        public VideoQuality $quality,
        public string $locale_code
    ) {}

    /**
     * Convert to array for JSON serialization
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name->value,
            'url' => $this->url,
            'file_url' => $this->file_url,
            'dubbing' => $this->dubbing,
            'quality' => $this->quality->value,
            'locale_code' => $this->locale_code,
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
