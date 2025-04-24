<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tags', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('slug', 128)->unique();
            $table->string('name', 128);
            $table->string('description', 512);
            $table->string('image', 2048)->nullable();
            $table->json('aliases')->default(DB::raw("'[]'::json"));
            $table->boolean('is_genre')->default(false);
            $table->string('meta_title', 128)->nullable();
            $table->string('meta_description', 376)->nullable();
            $table->string('meta_image', 2048)->nullable();
            $table->timestamps();
        });

        // Добавляем полнотекстовый поиск
        DB::unprepared("
            ALTER TABLE tags
            ADD COLUMN searchable tsvector GENERATED ALWAYS AS (
                setweight(to_tsvector('ukrainian', name), 'A') ||
                setweight(to_tsvector('ukrainian', description), 'B') ||
                setweight(to_tsvector('ukrainian', coalesce(aliases::text, '')), 'C')
            ) STORED
        ");

        DB::unprepared('CREATE INDEX tags_searchable_index ON tags USING GIN (searchable)');
        DB::unprepared('CREATE INDEX tags_trgm_name_idx ON tags USING GIN (name gin_trgm_ops)');
    }

    public function down(): void
    {
        Schema::dropIfExists('tags');
    }
};
