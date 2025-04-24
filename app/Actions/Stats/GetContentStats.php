<?php

namespace App\Actions\Stats;

use App\DTOs\Stats\StatsDTO;
use App\Models\Comment;
use App\Models\Episode;
use App\Models\Movie;
use App\Models\Person;
use App\Models\Rating;
use App\Models\Selection;
use App\Models\Studio;
use App\Models\Tag;
use App\Models\UserList;
use Carbon\Carbon;
use Lorisleiva\Actions\Concerns\AsAction;

class GetContentStats
{
    use AsAction;

    /**
     * Get content statistics.
     *
     * @param  StatsDTO  $dto
     * @return array
     */
    public function handle(StatsDTO $dto): array
    {
        $days = $dto->days;
        
        return [
            'movies' => [
                'total' => Movie::count(),
                'new_this_period' => Movie::where('created_at', '>=', Carbon::now()->subDays($days))->count(),
                'by_kind' => [
                    'movie' => Movie::where('kind', 'movie')->count(),
                    'tv_series' => Movie::where('kind', 'tv_series')->count(),
                    'animated_movie' => Movie::where('kind', 'animated_movie')->count(),
                    'animated_series' => Movie::where('kind', 'animated_series')->count(),
                ],
            ],
            'episodes' => [
                'total' => Episode::count(),
                'new_this_period' => Episode::where('created_at', '>=', Carbon::now()->subDays($days))->count(),
            ],
            'people' => [
                'total' => Person::count(),
                'new_this_period' => Person::where('created_at', '>=', Carbon::now()->subDays($days))->count(),
            ],
            'studios' => [
                'total' => Studio::count(),
                'new_this_period' => Studio::where('created_at', '>=', Carbon::now()->subDays($days))->count(),
            ],
            'tags' => [
                'total' => Tag::count(),
                'new_this_period' => Tag::where('created_at', '>=', Carbon::now()->subDays($days))->count(),
            ],
            'selections' => [
                'total' => Selection::count(),
                'new_this_period' => Selection::where('created_at', '>=', Carbon::now()->subDays($days))->count(),
            ],
            'comments' => [
                'total' => Comment::count(),
                'new_this_period' => Comment::where('created_at', '>=', Carbon::now()->subDays($days))->count(),
            ],
            'ratings' => [
                'total' => Rating::count(),
                'new_this_period' => Rating::where('created_at', '>=', Carbon::now()->subDays($days))->count(),
            ],
            'user_lists' => [
                'total' => UserList::count(),
                'new_this_period' => UserList::where('created_at', '>=', Carbon::now()->subDays($days))->count(),
            ],
        ];
    }
}
