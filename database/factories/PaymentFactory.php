<?php

namespace Database\Factories;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Models\Tariff;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    public function definition(): array
    {
        $tariff = Tariff::query()->inRandomOrder()->first() ?? Tariff::factory()->create();
        $status = fake()->randomElement(PaymentStatus::cases());
        
        $liqpayData = [
            'payment_id' => Str::random(20),
            'status' => $status->value,
            'paytype' => fake()->randomElement(['card', 'privat24', 'masterpass', 'moment_part', 'cash', 'invoice']),
            'acq_id' => fake()->numberBetween(1, 10000),
            'order_id' => Str::random(10),
            'liqpay_order_id' => Str::random(15),
            'description' => "Payment for {$tariff->name} tariff",
            'sender_card_mask2' => fake()->creditCardNumber(),
            'sender_card_bank' => fake()->randomElement(['pb', 'mono', 'pumb', 'otp', 'aval']),
            'sender_card_type' => fake()->randomElement(['visa', 'mastercard']),
            'sender_first_name' => fake()->firstName(),
            'sender_last_name' => fake()->lastName(),
            'sender_phone' => fake()->phoneNumber(),
            'completion_date' => now()->format('Y-m-d H:i:s'),
        ];

        return [
            'user_id' => User::query()->inRandomOrder()->value('id') ?? User::factory(),
            'tariff_id' => $tariff->id,
            'amount' => $tariff->price,
            'currency' => $tariff->currency,
            'payment_method' => 'LiqPay',
            'transaction_id' => Str::uuid(),
            'status' => $status,
            'liqpay_data' => json_encode($liqpayData),
        ];
    }

    /**
     * Configure the payment as successful.
     */
    public function successful(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PaymentStatus::SUCCESS,
            'liqpay_data' => json_encode(array_merge(
                json_decode($attributes['liqpay_data'] ?? '{}', true),
                ['status' => PaymentStatus::SUCCESS->value]
            )),
        ]);
    }

    /**
     * Configure the payment as pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PaymentStatus::PENDING,
            'liqpay_data' => json_encode(array_merge(
                json_decode($attributes['liqpay_data'] ?? '{}', true),
                ['status' => PaymentStatus::PENDING->value]
            )),
        ]);
    }

    /**
     * Configure the payment as failed.
     */
    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PaymentStatus::FAILED,
            'liqpay_data' => json_encode(array_merge(
                json_decode($attributes['liqpay_data'] ?? '{}', true),
                ['status' => PaymentStatus::FAILED->value]
            )),
        ]);
    }

    /**
     * Configure the payment as refunded.
     */
    public function refunded(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PaymentStatus::REFUNDED,
            'liqpay_data' => json_encode(array_merge(
                json_decode($attributes['liqpay_data'] ?? '{}', true),
                ['status' => PaymentStatus::REFUNDED->value]
            )),
        ]);
    }
}
