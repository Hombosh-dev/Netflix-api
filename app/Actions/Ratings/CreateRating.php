<?php

namespace App\Actions\Ratings;

use App\DTOs\Ratings\RatingStoreDTO;
use App\Models\Rating;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateRating
{
    use AsAction;

    /**
     * Create a new rating.
     *
     * @param  RatingStoreDTO  $dto
     * @return Rating
     */
    public function handle(RatingStoreDTO $dto): Rating
    {
        // Check if rating already exists
        $existingRating = Rating::where('user_id', $dto->userId)
            ->where('movie_id', $dto->movieId)
            ->first();
            
        if ($existingRating) {
            // Update existing rating
            $existingRating->number = $dto->number;
            $existingRating->review = $dto->review;
            $existingRating->save();
            
            return $existingRating;
        }
        
        // Create new rating
        $rating = new Rating();
        $rating->user_id = $dto->userId;
        $rating->movie_id = $dto->movieId;
        $rating->number = $dto->number;
        $rating->review = $dto->review;
        $rating->save();
        
        return $rating;
    }
}
