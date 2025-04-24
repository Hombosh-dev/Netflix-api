<?php

namespace App\Interfaces;

use Illuminate\Database\Eloquent\Relations\MorphMany;

interface Commentable
{
    /**
     * Get all comments associated with this model.
     */
    public function comments(): MorphMany;
}
