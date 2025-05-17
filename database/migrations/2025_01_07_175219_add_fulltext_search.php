<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Skip fulltext search setup for now
        DB::unprepared('CREATE EXTENSION IF NOT EXISTS pg_trgm');
    }


    public function down(): void
    {
        DB::statement('DROP TEXT SEARCH CONFIGURATION IF EXISTS ukrainian;');
        DB::statement('DROP TEXT SEARCH DICTIONARY IF EXISTS ukrainian_huns;');
        DB::statement('DROP TEXT SEARCH DICTIONARY IF EXISTS ukrainian_stem;');
    }
};
