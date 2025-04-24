<?php

namespace Database\Seeders;

use App\Models\Tariff;
use App\Models\User;
use App\Models\UserSubscription;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class UserSubscriptionSeeder extends Seeder
{
    public function run(): void
    {
        // Create some random subscriptions
        UserSubscription::factory(15)->create();
        
        // Create active subscriptions for specific users
        $users = User::take(5)->get();
        $tariffs = Tariff::where('is_active', true)->get();
        
        foreach ($users as $user) {
            $tariff = $tariffs->random();
            $startDate = now()->subDays(rand(1, $tariff->duration_days - 1));
            $endDate = Carbon::parse($startDate)->addDays($tariff->duration_days);
            
            UserSubscription::factory()->create([
                'user_id' => $user->id,
                'tariff_id' => $tariff->id,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'is_active' => true,
                'auto_renew' => true,
            ]);
        }
        
        // Create some expired subscriptions
        UserSubscription::factory(8)->expired()->create();
        
        // Create subscriptions for admin and moderator
        $admin = User::where('email', 'admin@gmail.com')->first();
        $moderator = User::where('email', 'moderator@gmail.com')->first();
        
        if ($admin) {
            $premiumTariff = Tariff::where('slug', 'premium')->first() ?? Tariff::factory()->create(['name' => 'Преміум']);
            UserSubscription::factory()->active()->create([
                'user_id' => $admin->id,
                'tariff_id' => $premiumTariff->id,
                'auto_renew' => true,
            ]);
        }
        
        if ($moderator) {
            $standardTariff = Tariff::where('slug', 'standard')->first() ?? Tariff::factory()->create(['name' => 'Стандартний']);
            UserSubscription::factory()->active()->create([
                'user_id' => $moderator->id,
                'tariff_id' => $standardTariff->id,
                'auto_renew' => true,
            ]);
        }
    }
}
