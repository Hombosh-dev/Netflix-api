<?php

namespace Database\Seeders;

use App\Models\Tariff;
use Illuminate\Database\Seeder;

class TariffSeeder extends Seeder
{
    public function run(): void
    {
        // Create predefined tariffs
        Tariff::factory()->create([
            'name' => 'Базовий',
            'description' => 'Базовий тариф з доступом до основного контенту в HD якості.',
            'price' => 99.00,
            'currency' => 'UAH',
            'duration_days' => 30,
            'features' => ['HD якість', 'Доступ до основного контенту', 'Перегляд на одному пристрої'],
            'slug' => 'basic',
            'meta_title' => 'Базовий тариф | ' . config('app.name'),
            'meta_description' => 'Базовий тариф з доступом до основного контенту в HD якості.',
        ]);

        Tariff::factory()->create([
            'name' => 'Стандартний',
            'description' => 'Стандартний тариф з доступом до розширеного контенту в HD якості та можливістю перегляду на двох пристроях одночасно.',
            'price' => 149.00,
            'currency' => 'UAH',
            'duration_days' => 30,
            'features' => ['HD якість', 'Розширений контент', 'Перегляд на двох пристроях', 'Без реклами'],
            'slug' => 'standard',
            'meta_title' => 'Стандартний тариф | ' . config('app.name'),
            'meta_description' => 'Стандартний тариф з доступом до розширеного контенту в HD якості та можливістю перегляду на двох пристроях одночасно.',
        ]);

        Tariff::factory()->create([
            'name' => 'Преміум',
            'description' => 'Преміум тариф з доступом до всього контенту в 4K якості, можливістю перегляду на чотирьох пристроях одночасно та завантаженням для офлайн перегляду.',
            'price' => 249.00,
            'currency' => 'UAH',
            'duration_days' => 30,
            'features' => ['4K якість', 'Весь контент', 'Перегляд на чотирьох пристроях', 'Без реклами', 'Завантаження для офлайн перегляду', 'Ексклюзивний контент'],
            'slug' => 'premium',
            'meta_title' => 'Преміум тариф | ' . config('app.name'),
            'meta_description' => 'Преміум тариф з доступом до всього контенту в 4K якості, можливістю перегляду на чотирьох пристроях одночасно та завантаженням для офлайн перегляду.',
        ]);

        // Create some random tariffs
        Tariff::factory(3)->create();

        // Create an inactive tariff
        Tariff::factory()->inactive()->create([
            'name' => 'Річний',
            'description' => 'Річний тариф з доступом до всього контенту в 4K якості та значною знижкою.',
            'price' => 1999.00,
            'currency' => 'UAH',
            'duration_days' => 365,
            'features' => ['4K якість', 'Весь контент', 'Перегляд на чотирьох пристроях', 'Без реклами', 'Завантаження для офлайн перегляду', 'Ексклюзивний контент', 'Знижка 30%'],
            'slug' => 'annual',
            'meta_title' => 'Річний тариф | ' . config('app.name'),
            'meta_description' => 'Річний тариф з доступом до всього контенту в 4K якості та значною знижкою.',
        ]);
    }
}
