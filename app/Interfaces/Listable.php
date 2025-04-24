<?php

namespace App\Interfaces;

use Illuminate\Database\Eloquent\Relations\MorphMany;

interface Listable
{
    /**
     * Get all user lists associated with this model.
     */
    public function userLists(): MorphMany;
}
