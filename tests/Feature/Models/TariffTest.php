<?php

use App\Models\Builders\TariffQueryBuilder;
use App\Models\Payment;
use App\Models\Tariff;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// Групуємо тести для моделі Tariff
test('tariff has correct query builder', function () {
    expect(Tariff::query())->toBeInstanceOf(TariffQueryBuilder::class);
});

test('tariff has correct relationships', function () {
    $tariff = new Tariff();

    expect($tariff->userSubscriptions())->toBeInstanceOf(HasMany::class)
        ->and($tariff->payments())->toBeInstanceOf(HasMany::class);
});

test('tariff has correct casts', function () {
    $tariff = new Tariff();
    $casts = $tariff->getCasts();

    expect($casts)->toBeArray()
        ->toHaveKeys(['price', 'features', 'is_active'])
        ->and($casts['price'])->toBe('decimal:2')
        ->and($casts['features'])->toBe(AsCollection::class)
        ->and($casts['is_active'])->toBe('boolean');
});

test('tariff query builder can filter active tariffs', function () {
    // Arrange
    $query = Tariff::active();

    // Act
    $sql = $query->toSql();

    // Assert
    expect($sql)->toContain('"is_active" = ?')
        ->and($query->getBindings())->toContain(true);
});

test('tariff query builder can filter inactive tariffs', function () {
    // Arrange
    $query = Tariff::inactive();

    // Act
    $sql = $query->toSql();

    // Assert
    expect($sql)->toContain('"is_active" = ?')
        ->and($query->getBindings())->toContain(false);
});

test('tariff query builder can filter by price less than', function () {
    // Arrange
    $price = 100.0;

    // Act
    $query = Tariff::withPriceLessThan($price);
    $sql = $query->toSql();

    // Assert
    expect($sql)->toContain('"price" <= ?')
        ->and($query->getBindings())->toContain($price);
});

test('tariff query builder can filter by price greater than', function () {
    // Arrange
    $price = 100.0;

    // Act
    $query = Tariff::withPriceGreaterThan($price);
    $sql = $query->toSql();

    // Assert
    expect($sql)->toContain('"price" >= ?')
        ->and($query->getBindings())->toContain($price);
});

test('tariff query builder can filter by price between', function () {
    // Arrange
    $minPrice = 100.0;
    $maxPrice = 200.0;

    // Act
    $query = Tariff::withPriceBetween($minPrice, $maxPrice);
    $sql = $query->toSql();

    // Assert
    expect($sql)->toContain('"price" between ? and ?')
        ->and($query->getBindings())->toContain($minPrice)
        ->and($query->getBindings())->toContain($maxPrice);
});

test('tariff query builder can order by price', function () {
    // Arrange
    $query = Tariff::orderByPrice('desc');

    // Act
    $sql = $query->toSql();

    // Assert
    expect($sql)->toContain('order by "price" desc');
});

test('tariff factory creates valid model', function () {
    // Act
    $tariff = Tariff::factory()->make();

    // Assert
    expect($tariff)->toBeInstanceOf(Tariff::class)
        ->and($tariff->name)->not->toBeEmpty()
        ->and($tariff->description)->not->toBeEmpty()
        ->and($tariff->price)->toBeGreaterThan(0)
        ->and($tariff->currency)->toBeIn(['UAH', 'USD', 'EUR'])
        ->and($tariff->duration_days)->toBeGreaterThan(0)
        ->and($tariff->features)->toBeInstanceOf(Collection::class)
        ->and($tariff->slug)->not->toBeEmpty();
});

test('can create tariff in database', function () {
    // Act
    $tariff = Tariff::factory()->create([
        'name' => 'Test Tariff',
        'description' => 'Test tariff description',
        'price' => 199.99,
        'currency' => 'UAH',
        'duration_days' => 30,
        'features' => ['HD quality', 'Ad-free'],
        'is_active' => true,
        'slug' => 'test-tariff',
    ]);

    // Assert
    expect($tariff)->toBeInstanceOf(Tariff::class)
        ->and($tariff->exists)->toBeTrue()
        ->and($tariff->name)->toBe('Test Tariff')
        ->and($tariff->description)->toBe('Test tariff description')
        ->and((float) $tariff->price)->toBe(199.99)
        ->and($tariff->currency)->toBe('UAH')
        ->and($tariff->duration_days)->toBe(30)
        ->and($tariff->features)->toBeInstanceOf(\Illuminate\Support\Collection::class)
        ->and($tariff->is_active)->toBeTrue()
        ->and($tariff->slug)->toBe('test-tariff')
        // Перевіряємо, що тариф дійсно збережено в базі даних
        ->and(Tariff::where('id', $tariff->id)->exists())->toBeTrue();

    // Альтернативний спосіб перевірки наявності в базі даних
    $this->assertDatabaseHas('tariffs', [
        'id' => $tariff->id,
        'name' => 'Test Tariff',
        'description' => 'Test tariff description',
        'price' => 199.99,
        'currency' => 'UAH',
        'duration_days' => 30,
        'is_active' => true,
        'slug' => 'test-tariff',
    ]);
});

test('can create inactive tariff', function () {
    // Act
    $tariff = Tariff::factory()->inactive()->create();

    // Assert
    expect($tariff->is_active)->toBeFalse();

    // Перевіряємо, що тариф дійсно збережено в базі даних
    $this->assertDatabaseHas('tariffs', [
        'id' => $tariff->id,
        'is_active' => false,
    ]);
});

test('can update tariff', function () {
    // Arrange
    $tariff = Tariff::factory()->create([
        'name' => 'Original Name',
        'description' => 'Original description',
        'price' => 99.99,
        'is_active' => true,
    ]);

    // Act - оновлюємо тариф
    $tariff->name = 'Updated Name';
    $tariff->description = 'Updated description';
    $tariff->price = 149.99;
    $tariff->is_active = false;
    $tariff->save();

    // Оновлюємо модель з бази даних
    $tariff->refresh();

    // Assert
    expect($tariff->name)->toBe('Updated Name')
        ->and($tariff->description)->toBe('Updated description')
        ->and((float)$tariff->price)->toBe(149.99)
        ->and($tariff->is_active)->toBeFalse();

    // Перевіряємо, що зміни збережені в базі даних
    $this->assertDatabaseHas('tariffs', [
        'id' => $tariff->id,
        'name' => 'Updated Name',
        'description' => 'Updated description',
        'price' => 149.99,
        'is_active' => false,
    ]);
});

test('tariff can have user subscriptions', function () {
    // Arrange
    $user = User::factory()->create();
    $tariff = Tariff::factory()->create();

    // Act
    $subscription = UserSubscription::factory()->create([
        'user_id' => $user->id,
        'tariff_id' => $tariff->id,
        'start_date' => now(),
        'end_date' => now()->addDays(30),
        'is_active' => true,
        'auto_renew' => false,
    ]);

    // Оновлюємо модель з бази даних
    $tariff->refresh();

    // Assert
    expect($tariff->userSubscriptions)->toHaveCount(1)
        ->and($tariff->userSubscriptions->first()->id)->toBe($subscription->id);

    // Перевіряємо, що підписка дійсно збережена в базі даних
    $this->assertDatabaseHas('user_subscriptions', [
        'id' => $subscription->id,
        'user_id' => $user->id,
        'tariff_id' => $tariff->id,
    ]);
});

test('tariff can have payments', function () {
    // Arrange
    $user = User::factory()->create();
    $tariff = Tariff::factory()->create();

    // Act
    $payment = Payment::factory()->create([
        'user_id' => $user->id,
        'tariff_id' => $tariff->id,
    ]);

    // Оновлюємо модель з бази даних
    $tariff->refresh();

    // Assert
    expect($tariff->payments)->toHaveCount(1)
        ->and($tariff->payments->first()->id)->toBe($payment->id);

    // Перевіряємо, що платіж дійсно збережений в базі даних
    $this->assertDatabaseHas('payments', [
        'id' => $payment->id,
        'user_id' => $user->id,
        'tariff_id' => $tariff->id,
    ]);
});

test('tariff uses HasSeo trait', function () {
    // Arrange
    $name = 'Test Tariff';

    // Act
    $slug = Tariff::generateSlug($name);

    // Assert
    expect($slug)->toStartWith('test-tariff-')
        ->and(strlen($slug))->toBeGreaterThan(strlen('test-tariff-'));
});
