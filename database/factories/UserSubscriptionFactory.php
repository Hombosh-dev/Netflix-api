<?php

namespace Database\Factories;

use App\Models\Tariff;
use App\Models\User;
use App\Models\UserSubscription;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserSubscription>
 */
class UserSubscriptionFactory extends Factory
{
    public function definition(): array
    {
        $tariff = Tariff::query()->inRandomOrder()->first() ?? Tariff::factory()->create();
        $startDate = fake()->dateTimeBetween('-1 year', 'now');
        $endDate = Carbon::parse($startDate)->addDays($tariff->duration_days);
        $isActive = $endDate->greaterThan(now());

        return [
            'user_id' => User::query()->inRandomOrder()->value('id') ?? User::factory(),
            'tariff_id' => $tariff->id,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'is_active' => $isActive,
            'auto_renew' => fake()->boolean(60), // 60% chance for auto-renewal
        ];
    }

    /**
     * Configure the subscription as active.
     */
    public function active(): static
    {
        return $this->state(function (array $attributes) {
            $tariff = Tariff::find($attributes['tariff_id']);
            $startDate = now()->subDays(fake()->numberBetween(1, $tariff->duration_days - 1));
            $endDate = Carbon::parse($startDate)->addDays($tariff->duration_days);
            
            return [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'is_active' => true,
            ];
        });
    }

    /**
     * Configure the subscription as expired.
     */
    public function expired(): static
    {
        return $this->state(function (array $attributes) {
            $tariff = Tariff::find($attributes['tariff_id']);
            $endDate = now()->subDays(fake()->numberBetween(1, 30));
            $startDate = Carbon::parse($endDate)->subDays($tariff->duration_days);
            
            return [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'is_active' => false,
            ];
        });
    }

    /**
     * Configure the subscription with auto-renewal enabled.
     */
    public function autoRenew(): static
    {
        return $this->state(fn (array $attributes) => [
            'auto_renew' => true,
        ]);
    }
}
