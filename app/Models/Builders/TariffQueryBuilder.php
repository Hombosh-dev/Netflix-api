<?php

namespace App\Models\Builders;

use Illuminate\Database\Eloquent\Builder;

class TariffQueryBuilder extends Builder
{
    /**
     * Get active tariffs.
     *
     * @return self
     */
    public function active(): self
    {
        return $this->where('is_active', true);
    }
    
    /**
     * Get inactive tariffs.
     *
     * @return self
     */
    public function inactive(): self
    {
        return $this->where('is_active', false);
    }
    
    /**
     * Get tariffs with price less than.
     *
     * @param float $price
     * @return self
     */
    public function withPriceLessThan(float $price): self
    {
        return $this->where('price', '<=', $price);
    }
    
    /**
     * Get tariffs with price greater than.
     *
     * @param float $price
     * @return self
     */
    public function withPriceGreaterThan(float $price): self
    {
        return $this->where('price', '>=', $price);
    }
    
    /**
     * Get tariffs with price between.
     *
     * @param float $minPrice
     * @param float $maxPrice
     * @return self
     */
    public function withPriceBetween(float $minPrice, float $maxPrice): self
    {
        return $this->whereBetween('price', [$minPrice, $maxPrice]);
    }
    
    /**
     * Get tariffs with duration in months.
     *
     * @param int $months
     * @return self
     */
    public function withDurationMonths(int $months): self
    {
        return $this->where('duration_months', $months);
    }
    
    /**
     * Order by price.
     *
     * @param string $direction
     * @return self
     */
    public function orderByPrice(string $direction = 'asc'): self
    {
        return $this->orderBy('price', $direction);
    }
    
    /**
     * Order by duration.
     *
     * @param string $direction
     * @return self
     */
    public function orderByDuration(string $direction = 'asc'): self
    {
        return $this->orderBy('duration_months', $direction);
    }
}
