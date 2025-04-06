<?php

namespace App\Actions\Tags;

use App\Models\Tags;

class ReadTagsAction
{
    /**
     * Повертає запис Tags за його ідентифікатором.
     *
     * @param string $id
     * @return Tags|null
     */
    public function execute(string $id): ?Tags
    {
        return Tags::find($id);
    }
}
