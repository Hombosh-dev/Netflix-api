<?php

namespace App\Actions\Stats;

use App\DTOs\Stats\StatsDTO;
use App\Models\User;
use Carbon\Carbon;
use Lorisleiva\Actions\Concerns\AsAction;

class GetUserStats
{
    use AsAction;

    /**
     * Get user statistics.
     *
     * @param  StatsDTO  $dto
     * @return array
     */
    public function handle(StatsDTO $dto): array
    {
        $days = $dto->days;
        
        $totalUsers = User::count();
        $newUsersToday = User::whereDate('created_at', Carbon::today())->count();
        $newUsersThisWeek = User::where('created_at', '>=', Carbon::now()->subDays(7))->count();
        $newUsersThisMonth = User::where('created_at', '>=', Carbon::now()->subDays(30))->count();
        $activeUsers = User::where('last_seen_at', '>=', Carbon::now()->subDays($days))->count();
        $bannedUsers = User::where('is_banned', true)->count();

        return [
            'total' => $totalUsers,
            'new_today' => $newUsersToday,
            'new_this_week' => $newUsersThisWeek,
            'new_this_month' => $newUsersThisMonth,
            'active_users' => $activeUsers,
            'banned_users' => $bannedUsers,
        ];
    }
}
