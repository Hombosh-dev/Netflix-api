<?php

use App\Models\Builders\UserSubscriptionQueryBuilder;
use App\Models\Tariff;
use App\Models\User;
use App\Models\UserSubscription;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// Групуємо тести для моделі UserSubscription
test('user subscription has correct query builder', function () {
    expect(UserSubscription::query())->toBeInstanceOf(UserSubscriptionQueryBuilder::class);
});

test('user subscription has correct relationships', function () {
    $subscription = new UserSubscription();

    expect($subscription->user())->toBeInstanceOf(BelongsTo::class)
        ->and($subscription->tariff())->toBeInstanceOf(BelongsTo::class);
});

test('user subscription has correct casts', function () {
    $subscription = new UserSubscription();
    $casts = $subscription->getCasts();

    expect($casts)->toBeArray()
        ->toHaveKeys(['start_date', 'end_date', 'is_active', 'auto_renew'])
        ->and($casts['start_date'])->toBe('datetime')
        ->and($casts['end_date'])->toBe('datetime')
        ->and($casts['is_active'])->toBe('boolean')
        ->and($casts['auto_renew'])->toBe('boolean');
});

test('user subscription query builder can filter by user', function () {
    // Arrange
    $userId = 'test-user-id';

    // Act
    $query = UserSubscription::forUser($userId);
    $sql = $query->toSql();

    // Assert
    expect($sql)->toContain('"user_id" = ?')
        ->and($query->getBindings())->toContain($userId);
});

test('user subscription query builder can filter by tariff', function () {
    // Arrange
    $tariffId = 'test-tariff-id';

    // Act
    $query = UserSubscription::forTariff($tariffId);
    $sql = $query->toSql();

    // Assert
    expect($sql)->toContain('"tariff_id" = ?')
        ->and($query->getBindings())->toContain($tariffId);
});

test('user subscription query builder can filter active subscriptions', function () {
    // Arrange
    $query = UserSubscription::active();

    // Act
    $sql = $query->toSql();

    // Assert
    expect($sql)->toContain('"is_active" = ?')
        ->and($query->getBindings())->toContain(true);
});

test('user subscription query builder can filter inactive subscriptions', function () {
    // Arrange
    $query = UserSubscription::inactive();

    // Act
    $sql = $query->toSql();

    // Assert
    expect($sql)->toContain('"is_active" = ?')
        ->and($query->getBindings())->toContain(false);
});

test('user subscription query builder can filter auto-renewable subscriptions', function () {
    // Arrange
    $query = UserSubscription::autoRenewable();

    // Act
    $sql = $query->toSql();

    // Assert
    expect($sql)->toContain('"auto_renew" = ?')
        ->and($query->getBindings())->toContain(true);
});

test('user subscription query builder can filter non-auto-renewable subscriptions', function () {
    // Arrange
    $query = UserSubscription::nonAutoRenewable();

    // Act
    $sql = $query->toSql();

    // Assert
    expect($sql)->toContain('"auto_renew" = ?')
        ->and($query->getBindings())->toContain(false);
});

test('user subscription query builder can filter expiring soon subscriptions', function () {
    // Arrange
    $days = 7;
    $query = UserSubscription::expiringSoon($days);

    // Act
    $sql = $query->toSql();

    // Assert
    expect($sql)->toContain('"is_active" = ?')
        ->and($sql)->toContain('"end_date" between ? and ?')
        ->and($query->getBindings())->toContain(true);
});

test('user subscription query builder can filter expired subscriptions', function () {
    // Arrange
    $query = UserSubscription::expired();

    // Act
    $sql = $query->toSql();

    // Assert
    expect($sql)->toContain('"end_date" < ?')
        ->and($sql)->toContain('"is_active" = ?')
        ->and($query->getBindings())->toContain(false);
});

test('user subscription factory creates valid model', function () {
    // Arrange
    $user = User::factory()->create();
    $tariff = Tariff::factory()->create();

    // Act
    $subscription = UserSubscription::factory()->make([
        'user_id' => $user->id,
        'tariff_id' => $tariff->id,
    ]);

    // Assert
    expect($subscription)->toBeInstanceOf(UserSubscription::class)
        ->and($subscription->user_id)->toBe($user->id)
        ->and($subscription->tariff_id)->toBe($tariff->id)
        ->and($subscription->start_date)->toBeInstanceOf(Carbon::class)
        ->and($subscription->end_date)->toBeInstanceOf(Carbon::class)
        ->and(is_bool($subscription->is_active))->toBeTrue()
        ->and(is_bool($subscription->auto_renew))->toBeTrue();
});

test('can create user subscription in database', function () {
    // Arrange
    $user = User::factory()->create();
    $tariff = Tariff::factory()->create();
    $startDate = now();
    $endDate = now()->addDays(30);

    // Act
    $subscription = UserSubscription::factory()->create([
        'user_id' => $user->id,
        'tariff_id' => $tariff->id,
        'start_date' => $startDate,
        'end_date' => $endDate,
        'is_active' => true,
        'auto_renew' => false,
    ]);

    // Assert
    expect($subscription)->toBeInstanceOf(UserSubscription::class)
        ->and($subscription->exists)->toBeTrue()
        ->and($subscription->user_id)->toBe($user->id)
        ->and($subscription->tariff_id)->toBe($tariff->id)
        ->and($subscription->start_date->toDateTimeString())->toBe($startDate->toDateTimeString())
        ->and($subscription->end_date->toDateTimeString())->toBe($endDate->toDateTimeString())
        ->and($subscription->is_active)->toBeTrue()
        ->and($subscription->auto_renew)->toBeFalse();

    // Перевіряємо, що підписка дійсно збережена в базі даних
    expect(UserSubscription::where('id', $subscription->id)->exists())->toBeTrue();

    // Альтернативний спосіб перевірки наявності в базі даних
    $this->assertDatabaseHas('user_subscriptions', [
        'id' => $subscription->id,
        'user_id' => $user->id,
        'tariff_id' => $tariff->id,
        'is_active' => true,
        'auto_renew' => false,
    ]);
});

test('can create expired user subscription', function () {
    // Arrange
    $user = User::factory()->create();
    $tariff = Tariff::factory()->create([
        'duration_days' => 30,
    ]);

    // Act
    $subscription = UserSubscription::factory()->expired()->create([
        'user_id' => $user->id,
        'tariff_id' => $tariff->id,
    ]);

    // Assert
    expect($subscription->is_active)->toBeFalse()
        ->and($subscription->end_date)->toBeLessThan(now());

    // Перевіряємо, що підписка дійсно збережена в базі даних
    $this->assertDatabaseHas('user_subscriptions', [
        'id' => $subscription->id,
        'user_id' => $user->id,
        'tariff_id' => $tariff->id,
        'is_active' => false,
    ]);
});

test('can create auto-renewable user subscription', function () {
    // Arrange
    $user = User::factory()->create();
    $tariff = Tariff::factory()->create();

    // Act
    $subscription = UserSubscription::factory()->autoRenew()->create([
        'user_id' => $user->id,
        'tariff_id' => $tariff->id,
    ]);

    // Assert
    expect($subscription->auto_renew)->toBeTrue();

    // Перевіряємо, що підписка дійсно збережена в базі даних
    $this->assertDatabaseHas('user_subscriptions', [
        'id' => $subscription->id,
        'user_id' => $user->id,
        'tariff_id' => $tariff->id,
        'auto_renew' => true,
    ]);
});

test('user subscription belongs to user', function () {
    // Arrange
    $user = User::factory()->create();
    $tariff = Tariff::factory()->create();
    $subscription = UserSubscription::factory()->create([
        'user_id' => $user->id,
        'tariff_id' => $tariff->id,
    ]);

    // Act - завантажуємо зв'язок
    $subscription->load('user');

    // Assert - перевіряємо зв'язок
    expect($subscription->user)->toBeInstanceOf(User::class)
        ->and($subscription->user->id)->toBe($user->id);
});

test('user subscription belongs to tariff', function () {
    // Arrange
    $user = User::factory()->create();
    $tariff = Tariff::factory()->create();
    $subscription = UserSubscription::factory()->create([
        'user_id' => $user->id,
        'tariff_id' => $tariff->id,
    ]);

    // Act - завантажуємо зв'язок
    $subscription->load('tariff');

    // Assert - перевіряємо зв'язок
    expect($subscription->tariff)->toBeInstanceOf(Tariff::class)
        ->and($subscription->tariff->id)->toBe($tariff->id);
});

test('user subscription is deleted when user is deleted', function () {
    // Arrange
    $user = User::factory()->create();
    $tariff = Tariff::factory()->create();
    $subscription = UserSubscription::factory()->create([
        'user_id' => $user->id,
        'tariff_id' => $tariff->id,
    ]);

    // Act - видаляємо користувача
    $user->delete();

    // Assert - перевіряємо, що підписка також видалена
    expect(UserSubscription::where('id', $subscription->id)->exists())->toBeFalse();

    // Альтернативний спосіб перевірки відсутності в базі даних
    $this->assertDatabaseMissing('user_subscriptions', [
        'id' => $subscription->id,
    ]);
});

test('user subscription is deleted when tariff is deleted', function () {
    // Arrange
    $user = User::factory()->create();
    $tariff = Tariff::factory()->create();
    $subscription = UserSubscription::factory()->create([
        'user_id' => $user->id,
        'tariff_id' => $tariff->id,
    ]);

    // Act - видаляємо тариф
    $tariff->delete();

    // Assert - перевіряємо, що підписка також видалена
    expect(UserSubscription::where('id', $subscription->id)->exists())->toBeFalse();

    // Альтернативний спосіб перевірки відсутності в базі даних
    $this->assertDatabaseMissing('user_subscriptions', [
        'id' => $subscription->id,
    ]);
});

test('can update user subscription', function () {
    // Arrange
    $user = User::factory()->create();
    $tariff = Tariff::factory()->create();
    $subscription = UserSubscription::factory()->create([
        'user_id' => $user->id,
        'tariff_id' => $tariff->id,
        'is_active' => true,
        'auto_renew' => false,
    ]);

    // Act - оновлюємо підписку
    $subscription->is_active = false;
    $subscription->auto_renew = true;
    $subscription->save();

    // Оновлюємо модель з бази даних
    $subscription->refresh();

    // Assert
    expect($subscription->is_active)->toBeFalse()
        ->and($subscription->auto_renew)->toBeTrue();

    // Перевіряємо, що зміни збережені в базі даних
    $this->assertDatabaseHas('user_subscriptions', [
        'id' => $subscription->id,
        'is_active' => false,
        'auto_renew' => true,
    ]);
});

test('isExpired method returns true for expired subscription', function () {
    // Arrange
    $user = User::factory()->create();
    $tariff = Tariff::factory()->create();
    $now = Carbon::now();
    $subscription = UserSubscription::factory()->create([
        'user_id' => $user->id,
        'tariff_id' => $tariff->id,
        'start_date' => $now->copy()->subDays(40),
        'end_date' => $now->copy()->subDays(10),
        'is_active' => false,
    ]);

    // Мокаємо поточний час для стабільності тесту
    Carbon::setTestNow($now);

    // Act & Assert
    expect($subscription->isExpired())->toBeTrue();

    // Відновлюємо нормальну поведінку Carbon
    Carbon::setTestNow();
});

test('isExpired method returns false for active subscription', function () {
    // Arrange
    $user = User::factory()->create();
    $tariff = Tariff::factory()->create();
    $now = Carbon::now();
    $subscription = UserSubscription::factory()->create([
        'user_id' => $user->id,
        'tariff_id' => $tariff->id,
        'start_date' => $now->copy()->subDays(10),
        'end_date' => $now->copy()->addDays(20),
        'is_active' => true,
    ]);

    // Мокаємо поточний час для стабільності тесту
    Carbon::setTestNow($now);

    // Act & Assert
    expect($subscription->isExpired())->toBeFalse();

    // Відновлюємо нормальну поведінку Carbon
    Carbon::setTestNow();
});

test('daysLeft method returns correct number of days for active subscription', function () {
    // Arrange
    $user = User::factory()->create();
    $tariff = Tariff::factory()->create();
    $now = Carbon::now();
    $daysLeft = 20;
    $subscription = UserSubscription::factory()->create([
        'user_id' => $user->id,
        'tariff_id' => $tariff->id,
        'start_date' => $now->copy()->subDays(10),
        'end_date' => $now->copy()->addDays($daysLeft),
        'is_active' => true,
    ]);

    // Мокаємо поточний час для стабільності тесту
    Carbon::setTestNow($now);

    // Act & Assert
    // Перевіряємо, що кількість днів близька до очікуваної
    expect($subscription->daysLeft())->toBeGreaterThanOrEqual($daysLeft - 1)
        ->toBelessThanOrEqual($daysLeft + 1);

    // Відновлюємо нормальну поведінку Carbon
    Carbon::setTestNow();
});

test('daysLeft method returns 0 for expired subscription', function () {
    // Arrange
    $user = User::factory()->create();
    $tariff = Tariff::factory()->create();
    $now = Carbon::now();
    $subscription = UserSubscription::factory()->create([
        'user_id' => $user->id,
        'tariff_id' => $tariff->id,
        'start_date' => $now->copy()->subDays(40),
        'end_date' => $now->copy()->subDays(10),
        'is_active' => false,
    ]);

    // Мокаємо поточний час для стабільності тесту
    Carbon::setTestNow($now);

    // Act & Assert
    expect($subscription->daysLeft())->toBe(0);

    // Відновлюємо нормальну поведінку Carbon
    Carbon::setTestNow();
});
