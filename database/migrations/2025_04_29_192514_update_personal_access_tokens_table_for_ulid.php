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
        // Спочатку видаляємо таблицю, якщо вона існує
        Schema::dropIfExists('personal_access_tokens');

        // Створюємо таблицю заново з правильними типами даних
        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('tokenable_type');
            $table->string('tokenable_id', 26);
            $table->string('name');
            $table->string('token', 64)->unique();
            $table->text('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index(['tokenable_type', 'tokenable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Видаляємо таблицю
        Schema::dropIfExists('personal_access_tokens');

        // Створюємо таблицю заново з оригінальними типами даних
        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->morphs('tokenable');
            $table->string('name');
            $table->string('token', 64)->unique();
            $table->text('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }
};
