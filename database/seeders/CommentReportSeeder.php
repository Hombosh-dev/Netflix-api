<?php

namespace Database\Seeders;

use App\Enums\CommentReportType;
use App\Models\Comment;
use App\Models\CommentReport;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class CommentReportSeeder extends Seeder
{
    public function run(): void
    {
        $comments = Comment::inRandomOrder()->take(round(Comment::count() * 0.1))->get();
        $users = User::all();

        foreach ($comments as $comment) {
            $reportsCount = rand(1, 3);

            $randomUsers = $users->where('id', '!=', $comment->user_id)
                ->random(min($reportsCount, $users->count()));

            foreach ($randomUsers as $user) {
                CommentReport::factory()
                    ->forCommentAndUser($comment, $user)
                    ->withType($this->getRandomReportType())
                    ->create();
            }
        }
    }

    /**
     * Повертає випадковий тип репорту
     */
    private function getRandomReportType(): CommentReportType
    {
        return Arr::random(CommentReportType::cases());
    }
}
