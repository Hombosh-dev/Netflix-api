<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Episode;
use App\Models\Movie;
use App\Models\Selection;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    public function run(): void
    {
        $movies = Movie::all();
        $episodes = Episode::all();
        $selections = Selection::all();
        $users = User::all();

        foreach ($movies as $movie) {
            Comment::factory()
                ->forCommentable($movie)
                ->forUser($users->random())
                ->count(rand(1, 5))
                ->create();
        }

        foreach ($episodes as $episode) {
            Comment::factory()
                ->forCommentable($episode)
                ->forUser($users->random())
                ->count(rand(1, 5))
                ->create();
        }

        foreach ($selections as $selection) {
            Comment::factory()
                ->forCommentable($selection)
                ->forUser($users->random())
                ->count(rand(1, 5))
                ->create();
        }

        $parentComments = Comment::roots()->inRandomOrder()->take(10)->get();

        foreach ($parentComments as $parent) {
            Comment::factory()
                ->replyTo($parent)
                ->forUser($users->random())
                ->count(rand(1, 3))
                ->create();
        }
    }
}
