<?php

namespace App\Interfaces;

use Illuminate\Database\Eloquent\Relations\MorphToMany;

interface Selectionable
{
    /**
     * Get all selections this model belongs to.
     */
    public function selections(): MorphToMany;
}
