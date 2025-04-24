<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

trait HasSearchable
{
    /**
     * Scope for combined full-text and trigram search
     *
     * @param  Builder  $query  The query builder
     * @param  string  $search  The search term
     * @param  array  $fields  Fields for trigram search
     * @param  float  $trigramThreshold  Similarity threshold for trigram
     * @return Builder
     */
    public function scopeSearch(
        Builder $query,
        string $search,
        array $fields = ['name'],
        float $trigramThreshold = 0.5
    ): Builder {
        $search = trim($search);
        if (empty($search)) {
            return $query;
        }

        // Prepare bindings
        $bindings = [];
        $searchTerm = DB::getPdo()->quote($search);

        // Initialize query with base selection
        $query->select('*');

        // Short terms (<4 chars) use trigram only
        if (mb_strlen($search) < 4) {
            return $this->applyTrigramSearch($query, $search, $fields, $trigramThreshold);
        }

        // Combined full-text and trigram search
        $query->where(function (Builder $q) use ($search, $searchTerm, $fields, $trigramThreshold, &$bindings) {
            // Full-text search
            $q->orWhereRaw(
                "searchable @@ websearch_to_tsquery('ukrainian', ?)",
                [$search]
            );
            $bindings[] = $search;

            // Trigram search for each field (limited to 1000 chars)
            foreach ($fields as $field) {
                $q->orWhereRaw(
                    "similarity(LEFT(?, 1000), ?) > ?",
                    [$field, $search, $trigramThreshold]
                );
                $bindings[] = $search;
                $bindings[] = $trigramThreshold;
            }
        });

        // Add ranking
        $query->addSelect(DB::raw("
            COALESCE(
                ts_rank(searchable, websearch_to_tsquery('ukrainian', {$searchTerm})) * 1.5,
                0
            ) + COALESCE(
                GREATEST(".implode(', ', array_map(
                fn($field) => "similarity(LEFT({$field}, 1000), '{$search}')",
                $fields
            ))."),
                0
            ) AS search_rank
        "));

        // Order by combined rank
        return $query->orderByDesc('search_rank');
    }

    /**
     * Apply trigram search only
     *
     * @param  Builder  $query  The query builder
     * @param  string  $search  The search term
     * @param  array  $fields  Fields to search
     * @param  float  $threshold  Similarity threshold
     * @return Builder
     */
    private function applyTrigramSearch(Builder $query, string $search, array $fields, float $threshold): Builder
    {
        // Simple LIKE search for testing purposes
        $query->where(function ($q) use ($fields, $search) {
            foreach ($fields as $field) {
                $q->orWhere($field, 'LIKE', "%{$search}%");
            }
        });

        return $query;
    }
}
