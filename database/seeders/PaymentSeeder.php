<?php

namespace Database\Seeders;

use App\Models\Payment;
use App\Models\Tariff;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        // Create successful payments for active subscriptions
        $subscriptions = UserSubscription::where('is_active', true)->get();
        
        foreach ($subscriptions as $subscription) {
            Payment::factory()->successful()->create([
                'user_id' => $subscription->user_id,
                'tariff_id' => $subscription->tariff_id,
                'amount' => Tariff::find($subscription->tariff_id)->price,
                'currency' => Tariff::find($subscription->tariff_id)->currency,
                'transaction_id' => Str::uuid(),
                'created_at' => $subscription->start_date,
            ]);
        }
        
        // Create some random payments with different statuses
        Payment::factory(10)->successful()->create();
        Payment::factory(5)->pending()->create();
        Payment::factory(3)->failed()->create();
        Payment::factory(2)->refunded()->create();
        
        // Create payment history for admin and moderator
        $admin = User::where('email', 'admin@gmail.com')->first();
        $moderator = User::where('email', 'moderator@gmail.com')->first();
        
        if ($admin) {
            $adminSubscription = UserSubscription::where('user_id', $admin->id)->first();
            if ($adminSubscription) {
                $tariff = Tariff::find($adminSubscription->tariff_id);
                
                // Create a few past payments for admin
                for ($i = 3; $i > 0; $i--) {
                    $date = now()->subMonths($i);
                    Payment::factory()->successful()->create([
                        'user_id' => $admin->id,
                        'tariff_id' => $tariff->id,
                        'amount' => $tariff->price,
                        'currency' => $tariff->currency,
                        'transaction_id' => Str::uuid(),
                        'created_at' => $date,
                    ]);
                }
            }
        }
        
        if ($moderator) {
            $moderatorSubscription = UserSubscription::where('user_id', $moderator->id)->first();
            if ($moderatorSubscription) {
                $tariff = Tariff::find($moderatorSubscription->tariff_id);
                
                // Create a few past payments for moderator
                for ($i = 2; $i > 0; $i--) {
                    $date = now()->subMonths($i);
                    Payment::factory()->successful()->create([
                        'user_id' => $moderator->id,
                        'tariff_id' => $tariff->id,
                        'amount' => $tariff->price,
                        'currency' => $tariff->currency,
                        'transaction_id' => Str::uuid(),
                        'created_at' => $date,
                    ]);
                }
                
                // Create one failed payment
                Payment::factory()->failed()->create([
                    'user_id' => $moderator->id,
                    'tariff_id' => $tariff->id,
                    'amount' => $tariff->price,
                    'currency' => $tariff->currency,
                    'transaction_id' => Str::uuid(),
                    'created_at' => now()->subMonths(3),
                ]);
            }
        }
    }
}
