<?php

namespace Tests\Feature\Models\Traits;

use App\Models\Movie;
use App\Models\Traits\HasSearchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

uses(RefreshDatabase::class);

// Створюємо тестову модель, яка використовує трейт HasSearchable
class TestSearchableModel extends Model
{
    use HasSearchable;

    protected $table = 'test_searchable_models';
    protected $fillable = ['name', 'description'];
}

// Підготовка тестового середовища
beforeEach(function () {
    // Створюємо тимчасову таблицю для тестової моделі
    if (!Schema::hasTable('test_searchable_models')) {
        Schema::create('test_searchable_models', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Додаємо колонку searchable для повнотекстового пошуку
        DB::statement('ALTER TABLE test_searchable_models ADD COLUMN searchable TSVECTOR');

        // Створюємо індекс для повнотекстового пошуку
        DB::statement('CREATE INDEX test_searchable_models_searchable_idx ON test_searchable_models USING GIN(searchable)');

        // Створюємо тригер для автоматичного оновлення колонки searchable
        DB::statement('
            CREATE TRIGGER test_searchable_models_searchable_trigger
            BEFORE INSERT OR UPDATE ON test_searchable_models
            FOR EACH ROW EXECUTE FUNCTION
            tsvector_update_trigger(searchable, \'pg_catalog.english\', name, description)
        ');
    }
});

// Очищення тестового середовища
afterEach(function () {
    // Видаляємо тимчасову таблицю після тестів
    Schema::dropIfExists('test_searchable_models');
});

// Тест для перевірки, що трейт HasSearchable надає метод search
test('has searchable trait provides search method', function () {
    // Arrange - створюємо тестові дані
    TestSearchableModel::create([
        'name' => 'Test Model',
        'description' => 'This is a test model for searching'
    ]);

    TestSearchableModel::create([
        'name' => 'Another Model',
        'description' => 'This is another test model'
    ]);

    // Act - виконуємо пошук
    $results = TestSearchableModel::search('test')->get();

    // Assert - перевіряємо результати
    expect($results)->toHaveCount(2);

    // Перевіряємо SQL запит
    $query = TestSearchableModel::search('test');
    $sql = $query->toSql();

    expect($sql)
        ->toContain('select *')
        ->toContain('ts_rank(searchable, websearch_to_tsquery');
});

// Тест для перевірки, що пошук повертає правильні результати
test('search returns relevant results', function () {
    // Arrange - створюємо тестові дані
    $model1 = TestSearchableModel::create([
        'name' => 'Test Model',
        'description' => 'This is a test model for searching'
    ]);

    $model2 = TestSearchableModel::create([
        'name' => 'Another Model',
        'description' => 'This has nothing to do with testing'
    ]);

    $model3 = TestSearchableModel::create([
        'name' => 'Something Else',
        'description' => 'Completely different content'
    ]);

    // Act - виконуємо пошук за словом "test"
    $results = TestSearchableModel::search('test')->get();

    // Assert - перевіряємо, що знайдено моделі, які містять слово "test"
    // Залежно від налаштувань повнотекстового пошуку, може бути знайдено більше моделей
    expect($results)->not->toBeEmpty()
        ->and($results->contains('id', $model1->id))->toBeTrue();
});

// Тест для перевірки, що модель Movie використовує трейт HasSearchable
test('movie model uses has searchable trait', function () {
    // Arrange - створюємо тестові дані
    $movie = Movie::factory()->create([
        'name' => 'Test Movie',
        'description' => 'This is a test movie for searching'
    ]);

    // Act - виконуємо пошук
    $query = Movie::search('test');
    $sql = $query->toSql();

    // Assert - перевіряємо SQL запит
    expect($sql)
        ->toContain('select *')
        ->toContain('ts_rank(searchable, websearch_to_tsquery');
});
