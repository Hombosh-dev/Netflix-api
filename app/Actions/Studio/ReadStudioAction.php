<?php

namespace App\Actions\Studio;

use App\Models\Studio;
use Lorisleiva\Actions\Concerns\AsAction;

class ReadStudioAction
{
    use AsAction;

    /**
     * Повертає запис Studio за його ідентифікатором.
     *
     * @param int $id
     * @return Studio|null
     */
    public function execute(int $id): ?Studio
    {
        return Studio::find($id);
    }
}
