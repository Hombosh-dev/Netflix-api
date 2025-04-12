<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Str;

trait HasSeo
{
    public static function generateSlug(string $value): string
    {
        return str($value)->slug().'-'.str(str()->random(6))->lower();
    }

    public static function makeMetaTitle(string $title): string
    {
        return $title.' | '.config('app.name');
    }

    public static function makeMetaDescription(string $description): string
    {
        return str()->length($description) > 376 ? str()->substr($description, 0, 373).'...' : $description;
    }

    public function scopeBySlug(Builder $query, string $slug): Builder
    {
        return $query->where('slug', $slug);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * @return array{
     *     max_size: int,
     *     accepted_types: string[],
     *     disk: string,
     *     directory: string
     * }
     */
    public static function metaImageConstraints(): array
    {
        return [
            'max_size' => 5120, // 5MB
            'accepted_types' => ['image/*'],
            'disk' => 'public',
            'directory' => 'meta/images',
        ];
    }
}
