<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create regular users
        User::factory(18)->create();

        // Create banned users
        User::factory(2)->banned()->create();

        // Create an admin user
        User::factory()->admin()->create([
            'name' => 'admin',
            'email' => 'admin@gmail.com',
            'allow_adult' => true,
            'is_banned' => false, // Ensure admin is not banned
        ]);

        // Create a moderator user
        User::factory()->moderator()->create([
            'name' => 'moderator',
            'email' => 'moderator@gmail.com',
            'is_banned' => false, // Ensure moderator is not banned
        ]);

        // Create a banned user with specific credentials
        User::factory()->banned()->create([
            'name' => 'banned_user',
            'email' => 'banned@gmail.com',
        ]);
    }
}
