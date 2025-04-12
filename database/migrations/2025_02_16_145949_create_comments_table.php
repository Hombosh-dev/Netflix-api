<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->ulidMorphs('commentable');
            $table->foreignUlid('user_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_spoiler')->default(false);
            $table->text('body');
            $table->timestamps();
        });

        Schema::table('comments', function (Blueprint $table) {
            $table->foreignUlid('parent_id')->nullable()->constrained('comments')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn('parent_id');
        });

        Schema::dropIfExists('comment_reports');
        Schema::dropIfExists('comment_likes');
        Schema::dropIfExists('comments');
    }
};
