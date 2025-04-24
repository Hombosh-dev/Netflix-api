<?php

namespace App\Models\Builders;

use Illuminate\Database\Eloquent\Builder;

class SelectionQueryBuilder extends Builder
{
    /**
     * Get published selections.
     *
     * @return self
     */
    public function published(): self
    {
        return $this->where('is_published', true);
    }

    /**
     * Get unpublished selections.
     *
     * @return self
     */
    public function unpublished(): self
    {
        return $this->where('is_published', false);
    }

    /**
     * Get selections by user.
     *
     * @param string $userId
     * @return self
     */
    public function byUser(string $userId): self
    {
        return $this->where('user_id', $userId);
    }

    /**
     * Get selections with movies.
     *
     * @return self
     */
    public function withMovies(): self
    {
        return $this->whereHas('movies');
    }

    /**
     * Get selections with persons.
     *
     * @return self
     */
    public function withPersons(): self
    {
        return $this->whereHas('persons');
    }

    /**
     * Get selections with comments.
     *
     * @return self
     */
    public function withComments(): self
    {
        return $this->whereHas('comments');
    }

    /**
     * Get selections with movie count.
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
