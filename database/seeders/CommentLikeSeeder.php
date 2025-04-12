<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\CommentLike;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CommentLikeSeeder extends Seeder
{
    public function run(): void
    {
        $comments = Comment::all();

        foreach ($comments as $comment) {
            $likesCount = rand(5, 20);
            $usersToLike = User::inRandomOrder()->take($likesCount)->get();

            foreach ($usersToLike as $user) {
                $isLike = rand(0, 1) === 1;

                CommentLike::create([
                    'comment_id' => $comment->id,
                    'user_id' => $user->id,
                    'is_liked' => $isLike,
                ]);
            }
        }
    }
}
