<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comment_reports', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('comment_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('user_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_viewed'); // true = liked, false = disliked
            $table->timestamps();

            ## Переробити на is_viewed. додати enum.

            $table->unique(['comment_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comment_likes');
    }
};
