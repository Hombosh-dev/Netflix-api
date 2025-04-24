<?php

namespace App\Models\Builders;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class TagQueryBuilder extends Builder
{
    /**
     * Filter by genre tags.
     *
     * @return self
     */
    public function genres(): self
    {
        return $this->where('is_genre', true);
    }
    
    /**
     * Filter by non-genre tags.
     *
     * @return self
     */
    public function nonGenres(): self
    {
        return $this->where('is_genre', false);
    }
    
    /**
     * Search for tags by name, aliases, or description.
     *
     * @param string $term
     * @return self
     */
    public function search(string $term): self
    {
        return $this
            ->select('*')
            ->addSelect(DB::raw("ts_rank(searchable, websearch_to_tsquery('ukrainian', ?)) AS rank"))
            ->addSelect(DB::raw("ts_headline('ukrainian', name, websearch_to_tsquery('ukrainian', ?), 'HighlightAll=true') AS name_highlight"))
            ->addSelect(DB::raw("ts_headline('ukrainian', aliases, websearch_to_tsquery('ukrainian', ?), 'HighlightAll=true') AS aliases_highlight"))
            ->addSelect(DB::raw('similarity(name, ?) AS similarity'))
            ->whereRaw("searchable @@ websearch_to_tsquery('ukrainian', ?)",
                [$term, $term, $term, $term, $term])
            ->orWhereRaw('name % ?', [$term])
            ->orderByDesc('rank')
            ->orderByDesc('similarity');
    }
    
    /**
     * Order tags by popularity (movie count).
     *
     * @return self
     */
    public function popular(): self
    {
        return $this->withCount('movies')
            ->orderByDesc('movies_count');
    }
}
