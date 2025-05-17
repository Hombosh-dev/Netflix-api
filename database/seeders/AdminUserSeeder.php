<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Створюємо нового адміністратора
        User::create([
            'name' => 'admin',
            'email' => 'admin@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password123'),
            'role' => Role::ADMIN->value,
            'allow_adult' => true,
            'is_banned' => false,
        ]);

        $this->command->info('Адміністратор успішно створений!');
        $this->command->info('Email: admin@example.com');
        $this->command->info('Пароль: password123');
    }
}
