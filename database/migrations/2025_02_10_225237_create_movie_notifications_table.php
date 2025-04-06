<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('movie_user_notifications', function (Blueprint $table) {
            $table->foreignUlid('user_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('movie_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->primary(['user_id', 'movie_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movie_notifications');
    }
};
