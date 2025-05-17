<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            TagSeeder::class,
            StudioSeeder::class,
            PersonSeeder::class,
            MovieSeeder::class,
            MovieImagesSeeder::class, // Сідер для завантаження зображень фільмів
            RatingSeeder::class,
            EpisodeSeeder::class, // Універсальний сідер для всіх епізодів
            SelectionSeeder::class,
            UserListSeeder::class,
            CommentSeeder::class,
            CommentLikeSeeder::class,
            CommentReportSeeder::class,
            TariffSeeder::class,
            UserSubscriptionSeeder::class,
            PaymentSeeder::class,
        ]);
    }
}
