<?php

namespace App\Models\Builders;

use Illuminate\Database\Eloquent\Builder;

class StudioQueryBuilder extends Builder
{
    /**
     * Filter by name.
     *
     * @param string $name
     * @return self
     */
    public function byName(string $name): self
    {
        return $this->where('name', 'like', '%'.$name.'%');
    }
    
    /**
     * Get studios with movies.
     *
     * @return self
     */
    public function withMovies(): self
    {
        return $this->whereHas('movies');
    }
    
    /**
     * Get studios with movie count.
     *
     * @return self
     */
    public function withMovieCount(): self
    {
        return $this->withCount('movies');
    }
    
    /**
     * Order by movie count.
     *
     * @param string $direction
     * @return self
     */
    public function orderByMovieCount(string $direction = 'desc'): self
    {
        return $this->withMovieCount()->orderBy('movies_count', $direction);
    }
}
