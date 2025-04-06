<?php

namespace App\Actions\Ratings;

use App\Models\Ratings;

class ReadRatingsAction
{
    /**
     * Повертає запис Ratings за його ідентифікатором.
     *
     * @param string $id
     * @return Ratings|null
     */
    public function execute(string $id): ?Ratings
    {
        return Ratings::find($id);
    }
}
