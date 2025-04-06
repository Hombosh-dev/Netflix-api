<?php

namespace App\Actions\Movie;

use App\Models\Movie;
use Lorisleiva\Actions\Concerns\AsAction;

class ReadMovieAction
{
    use AsAction;

    /**
     * Повертає запис Movie за його ідентифікатором.
     *
     * @param int $id
     * @return Movie|null
     */
    public function execute(int $id): ?Movie
    {
        return Movie::find($id);
    }
}
