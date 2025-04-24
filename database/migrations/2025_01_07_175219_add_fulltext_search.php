<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        $tsearchPath = env('POSTGRES_TSEARCH_PATH');

        $files = [
            'uk_ua.affix' => "$tsearchPath/uk_ua.affix",
            'uk_ua.dict' => "$tsearchPath/uk_ua.dict",
            'ukrainian.stop' => "$tsearchPath/ukrainian.stop",
        ];

        foreach ($files as $source => $destination) {
            $sourcePath = base_path("database/fts-dict/$source");

            if (!file_exists($sourcePath)) {
                throw new RuntimeException("Файл $sourcePath не знайдено");
            }

            if (file_exists($destination)) {
                continue;
            }

            if (!copy($sourcePath, $destination)) {
                throw new RuntimeException("Не вдалося скопіювати $sourcePath до $destination");
            }
        }

        DB::statement('DROP TEXT SEARCH DICTIONARY IF EXISTS ukrainian_huns CASCADE');
        DB::statement('DROP TEXT SEARCH DICTIONARY IF EXISTS ukrainian_stem CASCADE');
        DB::statement('DROP TEXT SEARCH CONFIGURATION IF EXISTS ukrainian CASCADE');

        DB::statement('
            CREATE TEXT SEARCH DICTIONARY ukrainian_huns (
                TEMPLATE = ispell,
                DictFile = uk_UA,
                AffFile = uk_UA,
                StopWords = ukrainian
            );
        ');

        DB::statement('
            CREATE TEXT SEARCH DICTIONARY ukrainian_stem (
                template = simple,
                stopwords = ukrainian
            );
        ');

        DB::statement('
            CREATE TEXT SEARCH CONFIGURATION ukrainian (PARSER=default);
        ');

        DB::statement('
            ALTER TEXT SEARCH CONFIGURATION ukrainian ALTER MAPPING FOR
                hword, hword_part, word WITH ukrainian_huns, ukrainian_stem;
        ');

        DB::statement('
            ALTER TEXT SEARCH CONFIGURATION ukrainian ALTER MAPPING FOR
                int, uint, numhword, numword, hword_numpart, email, float, file, url, url_path, version, host, sfloat WITH simple;
        ');

        DB::statement('
            ALTER TEXT SEARCH CONFIGURATION ukrainian ALTER MAPPING FOR
                asciihword, asciiword, hword_asciipart WITH english_stem;
        ');
        DB::unprepared('CREATE EXTENSION IF NOT EXISTS pg_trgm');
    }

    
    public function down(): void
    {
        DB::statement('DROP TEXT SEARCH CONFIGURATION IF EXISTS ukrainian;');
        DB::statement('DROP TEXT SEARCH DICTIONARY IF EXISTS ukrainian_huns;');
        DB::statement('DROP TEXT SEARCH DICTIONARY IF EXISTS ukrainian_stem;');
    }
};
