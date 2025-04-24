<?php

namespace App\Models\Builders;

use App\Enums\Kind;
use App\Enums\Status;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class MovieQueryBuilder extends Builder
{
    /**
     * Include average rating in the query.
     *
     * @return self
     */
    public function withAverageRating(): self
    {
        return $this->withAvg('ratings', 'number');
    }
    
    /**
     * Get popular movies based on user lists count.
     *
     * @return self
     */
    public function popular(): self
    {
        return $this->withCount('userLists')
            ->orderByDesc('user_lists_count');
    }
    
    /**
     * Get trending movies based on recent ratings and comments.
     *
     * @param int $days Number of days to consider
     * @return self
     */
    public function trending(int $days = 7): self
    {
        $date = Carbon::now()->subDays($days);
        
        return $this->withCount(['ratings' => function ($query) use ($date) {
                $query->where('created_at', '>=', $date);
            }])
            ->withCount(['comments' => function ($query) use ($date) {
                $query->where('created_at', '>=', $date);
            }])
            ->orderByRaw('(ratings_count * 2 + comments_count) DESC');
    }
    
    /**
     * Filter by movie kind.
     *
     * @param Kind $kind
     * @return self
     */
    public function ofKind(Kind $kind): self
    {
        return $this->where('kind', $kind->value);
    }
    
    /**
     * Filter by movie status.
     *
     * @param Status $status
     * @return self
     */
    public function withStatus(Status $status): self
    {
        return $this->where('status', $status->value);
    }
    
    /**
     * Filter by minimum IMDb score.
     *
     * @param float $score
     * @return self
     */
    public function withImdbScoreGreaterThan(float $score): self
    {
        return $this->where('imdb_score', '>=', $score);
    }
    
    /**
     * Filter by release year.
     *
     * @param int $year
     * @return self
     */
    public function releasedInYear(int $year): self
    {
        return $this->whereYear('first_air_date', $year);
    }
    
    /**
     * Get recently added movies.
     *
     * @param int $limit
     * @return self
     */
    public function recentlyAdded(int $limit = 10): self
    {
        return $this->orderByDesc('created_at')->limit($limit);
    }
    
    /**
     * Get movies with specific tags.
     *
     * @param array $tagIds
     * @return self
     */
    public function withTags(array $tagIds): self
    {
        return $this->whereHas('tags', function ($query) use ($tagIds) {
            $query->whereIn('tags.id', $tagIds);
        });
    }
    
    /**
     * Get movies with specific persons.
     *
     * @param array $personIds
     * @return self
     */
    public function withPersons(array $personIds): self
    {
        return $this->whereHas('persons', function ($query) use ($personIds) {
            $query->whereIn('persons.id', $personIds);
        });
    }
    
    /**
     * Get movies from specific countries.
     *
     * @param array $countryCodes
     * @return self
     */
    public function fromCountries(array $countryCodes): self
    {
        return $this->where(function ($query) use ($countryCodes) {
            foreach ($countryCodes as $countryCode) {
                $query->orWhereJsonContains('countries', $countryCode);
            }
        });
    }
}
