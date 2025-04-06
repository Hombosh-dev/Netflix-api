<?php

namespace App\Actions\Selection;
use App\Models\Selection;

class ReadSelectionAction
{
    /**
     * Повертає запис Selection за його ідентифікатором.
     *
     * @param string $id
     * @return Selection|null
     */
    public function execute(string $id): ?Selection
    {
        return Selection::find($id);
    }
}
