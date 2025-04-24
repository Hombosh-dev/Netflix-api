<?php

namespace App\Models\Builders;

use App\Enums\Gender;
use App\Enums\PersonType;
use Illuminate\Database\Eloquent\Builder;

class PersonQueryBuilder extends Builder
{
    /**
     * Filter by person type.
     *
     * @param PersonType $type
     * @return self
     */
    public function byType(PersonType $type): self
    {
        return $this->where('type', $type->value);
    }
    
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
     * Filter by gender.
     *
     * @param Gender|string $gender
     * @return self
     */
    public function byGender(Gender|string $gender): self
    {
        if ($gender instanceof Gender) {
            return $this->where('gender', $gender->value);
        }
        
        return $this->where('gender', $gender);
    }
    
    /**
     * Get actors.
     *
     * @return self
     */
    public function actors(): self
    {
        return $this->byType(PersonType::ACTOR);
    }
    
    /**
     * Get directors.
     *
     * @return self
     */
    public function directors(): self
    {
        return $this->byType(PersonType::DIRECTOR);
    }
    
    /**
     * Get writers.
     *
     * @return self
     */
    public function writers(): self
    {
        return $this->byType(PersonType::WRITER);
    }
    
    /**
     * Get persons with movies.
     *
     * @return self
     */
    public function withMovies(): self
    {
        return $this->whereHas('movies');
    }
    
    /**
     * Get persons with movie count.
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
