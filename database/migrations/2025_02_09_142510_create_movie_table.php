<?php

namespace App\Http\Controllers;

use App\Enums\Kind;
use App\Enums\Status;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('movies', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->json('api_sources')->default(DB::raw("'[]'::json"));
            $table->string('slug', 128)->unique();
            $table->string('name', 248);
            $table->text('description');
            $table->string('image_name', 2048);
            $table->json('aliases')->default(DB::raw("'[]'::json"));
            $table->foreignUlid('studio_id')->constrained()->cascadeOnDelete();
            $table->json('countries')->default(DB::raw("'[]'::json"));
            $table->string('poster', 2048)->nullable();
            $table->integer('duration')->nullable();
            $table->integer('episodes_count')->nullable();
            $table->date('first_air_date')->nullable();
            $table->date('last_air_date')->nullable();
            $table->decimal('imdb_score', 4, 2)->nullable();
            $table->json('attachments')->default(DB::raw("'[]'::json"));
            $table->json('related')->default(DB::raw("'[]'::json"));
            $table->json('similars')->default(DB::raw("'[]'::json"));
            $table->boolean('is_published')->default(false);
            $table->string('meta_title', 128)->nullable();
            $table->string('meta_description', 376)->nullable();
            $table->string('meta_image', 2048)->nullable();
            $table->timestamps();
        });

        Schema::table('movies', function (Blueprint $table) {
            $table->enumAlterColumn('kind', 'kind', Kind::class);
            $table->enumAlterColumn('status', 'status', Status::class);
        });

        DB::unprepared("
            ALTER TABLE movies
            ADD COLUMN searchable tsvector GENERATED ALWAYS AS (
                setweight(to_tsvector('ukrainian', name), 'A') ||
                setweight(to_tsvector('ukrainian', aliases), 'A') ||
                setweight(to_tsvector('ukrainian', description), 'B')
            ) STORED
        ");

        DB::unprepared('CREATE INDEX movies_searchable_index ON movies USING GIN (searchable)');
        DB::unprepared('CREATE INDEX movies_trgm_name_idx ON movies USING GIN (name gin_trgm_ops)');
    }

    public function down(): void
    {
        Schema::dropIfExists('movies');

        DB::unprepared('DROP TYPE status');
        DB::unprepared('DROP TYPE kind');

        DB::unprepared('DROP INDEX IF EXISTS movies_searchable_index');
        DB::unprepared('DROP INDEX IF EXISTS movies_trgm_name_idx');
    }
};
