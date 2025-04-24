<?php

use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Models\Tariff;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// Групуємо тести для моделі Payment
test('payment has correct relationships', function () {
    $payment = new Payment();

    expect($payment->user())->toBeInstanceOf(BelongsTo::class)
        ->and($payment->tariff())->toBeInstanceOf(BelongsTo::class);
});

test('payment has correct casts', function () {
    $payment = new Payment();
    $casts = $payment->getCasts();

    expect($casts)->toBeArray()
        ->toHaveKeys(['status', 'amount', 'liqpay_data'])
        ->and($casts['status'])->toBe(PaymentStatus::class)
        ->and($casts['amount'])->toBe('decimal:2')
        ->and($casts['liqpay_data'])->toBe('Illuminate\Database\Eloquent\Casts\AsCollection');
});

test('payment factory creates valid model', function () {
    // Arrange
    $user = User::factory()->create();
    $tariff = Tariff::factory()->create([
        'name' => 'Premium',
        'price' => 199.99,
        'currency' => 'UAH',
        'duration_days' => 30,
        'is_active' => true,
    ]);

    // Act
    $payment = Payment::factory()->make([
        'user_id' => $user->id,
        'tariff_id' => $tariff->id,
        'amount' => 199.99,
        'currency' => 'UAH',
        'payment_method' => 'card',
        'transaction_id' => 'txn_' . uniqid(),
        'status' => PaymentStatus::PENDING,
    ]);

    // Assert
    expect($payment)->toBeInstanceOf(Payment::class)
        ->and($payment->user_id)->toBe($user->id)
        ->and($payment->tariff_id)->toBe($tariff->id)
        ->and((float)$payment->amount)->toBe(199.99)
        ->and($payment->currency)->toBe('UAH')
        ->and($payment->payment_method)->toBe('card')
        ->and($payment->status)->toBe(PaymentStatus::PENDING);
});

// Додаємо тест для створення платежу в базі даних
test('can create payment in database', function () {
    // Arrange
    $user = User::factory()->create();
    $tariff = Tariff::factory()->create([
        'name' => 'Premium',
        'price' => 199.99,
        'currency' => 'UAH',
        'duration_days' => 30,
        'is_active' => true,
    ]);

    $transactionId = 'txn_' . uniqid();

    // Act
    $payment = Payment::factory()->create([
        'user_id' => $user->id,
        'tariff_id' => $tariff->id,
        'amount' => 199.99,
        'currency' => 'UAH',
        'payment_method' => 'card',
        'transaction_id' => $transactionId,
        'status' => PaymentStatus::PENDING,
        'liqpay_data' => [
            'order_id' => 'order_123',
            'payment_id' => 'payment_456',
        ],
    ]);

    // Assert
    expect($payment)->toBeInstanceOf(Payment::class)
        ->and($payment->exists)->toBeTrue()
        ->and($payment->user_id)->toBe($user->id)
        ->and($payment->tariff_id)->toBe($tariff->id)
        ->and((float)$payment->amount)->toBe(199.99)
        ->and($payment->currency)->toBe('UAH')
        ->and($payment->payment_method)->toBe('card')
        ->and($payment->transaction_id)->toBe($transactionId)
        ->and($payment->status)->toBe(PaymentStatus::PENDING)
        ->and($payment->liqpay_data->toArray())->toBe([
            'order_id' => 'order_123',
            'payment_id' => 'payment_456',
        ]);

    // Перевіряємо, що платіж дійсно збережений в базі даних
    expect(Payment::where('id', $payment->id)->exists())->toBeTrue();

    // Альтернативний спосіб перевірки наявності в базі даних
    $this->assertDatabaseHas('payments', [
        'id' => $payment->id,
        'user_id' => $user->id,
        'tariff_id' => $tariff->id,
        'amount' => 199.99,
        'currency' => 'UAH',
        'payment_method' => 'card',
        'transaction_id' => $transactionId,
        'status' => PaymentStatus::PENDING->value,
    ]);
});

// Додаємо тест для перевірки унікальності transaction_id
test('transaction_id must be unique', function () {
    // Arrange
    $user = User::factory()->create();
    $tariff = Tariff::factory()->create([
        'name' => 'Premium',
        'price' => 199.99,
        'currency' => 'UAH',
        'duration_days' => 30,
        'is_active' => true,
    ]);

    $transactionId = 'txn_' . uniqid();

    // Act - створюємо перший платіж
    Payment::factory()->create([
        'user_id' => $user->id,
        'tariff_id' => $tariff->id,
        'amount' => 199.99,
        'currency' => 'UAH',
        'payment_method' => 'card',
        'transaction_id' => $transactionId,
        'status' => PaymentStatus::PENDING,
    ]);

    // Assert - перевіряємо, що другий платіж з тим самим transaction_id викликає помилку
    $this->expectException(\Illuminate\Database\QueryException::class);

    Payment::factory()->create([
        'user_id' => $user->id,
        'tariff_id' => $tariff->id,
        'amount' => 199.99,
        'currency' => 'UAH',
        'payment_method' => 'card',
        'transaction_id' => $transactionId, // Той самий transaction_id
        'status' => PaymentStatus::PENDING,
    ]);
});

// Додаємо тест для перевірки зв'язків з іншими моделями
test('payment belongs to user and tariff', function () {
    // Arrange
    $user = User::factory()->create();
    $tariff = Tariff::factory()->create([
        'name' => 'Premium',
        'price' => 199.99,
        'currency' => 'UAH',
        'duration_days' => 30,
        'is_active' => true,
    ]);

    $payment = Payment::factory()->create([
        'user_id' => $user->id,
        'tariff_id' => $tariff->id,
        'amount' => 199.99,
        'currency' => 'UAH',
        'payment_method' => 'card',
        'transaction_id' => 'txn_' . uniqid(),
        'status' => PaymentStatus::PENDING,
    ]);

    // Act - завантажуємо зв'язки
    $payment->load(['user', 'tariff']);

    // Assert - перевіряємо зв'язки
    expect($payment->user)->toBeInstanceOf(User::class)
        ->and($payment->user->id)->toBe($user->id)
        ->and($payment->tariff)->toBeInstanceOf(Tariff::class)
        ->and($payment->tariff->id)->toBe($tariff->id);
});

// Додаємо тест для перевірки каскадного видалення при видаленні користувача
test('payment is deleted when user is deleted', function () {
    // Arrange
    $user = User::factory()->create();
    $tariff = Tariff::factory()->create([
        'name' => 'Premium',
        'price' => 199.99,
        'currency' => 'UAH',
        'duration_days' => 30,
        'is_active' => true,
    ]);

    $payment = Payment::factory()->create([
        'user_id' => $user->id,
        'tariff_id' => $tariff->id,
        'amount' => 199.99,
        'currency' => 'UAH',
        'payment_method' => 'card',
        'transaction_id' => 'txn_' . uniqid(),
        'status' => PaymentStatus::PENDING,
    ]);

    // Act - видаляємо користувача
    $user->delete();

    // Assert - перевіряємо, що платіж також видалено
    expect(Payment::where('id', $payment->id)->exists())->toBeFalse();

    // Альтернативний спосіб перевірки відсутності в базі даних
    $this->assertDatabaseMissing('payments', [
        'id' => $payment->id,
    ]);
});

// Додаємо тест для перевірки каскадного видалення при видаленні тарифу
test('payment is deleted when tariff is deleted', function () {
    // Arrange
    $user = User::factory()->create();
    $tariff = Tariff::factory()->create([
        'name' => 'Premium',
        'price' => 199.99,
        'currency' => 'UAH',
        'duration_days' => 30,
        'is_active' => true,
    ]);

    $payment = Payment::factory()->create([
        'user_id' => $user->id,
        'tariff_id' => $tariff->id,
        'amount' => 199.99,
        'currency' => 'UAH',
        'payment_method' => 'card',
        'transaction_id' => 'txn_' . uniqid(),
        'status' => PaymentStatus::PENDING,
    ]);

    // Act - видаляємо тариф
    $tariff->delete();

    // Assert - перевіряємо, що платіж також видалено
    expect(Payment::where('id', $payment->id)->exists())->toBeFalse();

    // Альтернативний спосіб перевірки відсутності в базі даних
    $this->assertDatabaseMissing('payments', [
        'id' => $payment->id,
    ]);
});

// Додаємо тест для перевірки зміни статусу платежу
test('can update payment status', function () {
    // Arrange
    $user = User::factory()->create();
    $tariff = Tariff::factory()->create([
        'name' => 'Premium',
        'price' => 199.99,
        'currency' => 'UAH',
        'duration_days' => 30,
        'is_active' => true,
    ]);

    $payment = Payment::factory()->create([
        'user_id' => $user->id,
        'tariff_id' => $tariff->id,
        'amount' => 199.99,
        'currency' => 'UAH',
        'payment_method' => 'card',
        'transaction_id' => 'txn_' . uniqid(),
        'status' => PaymentStatus::PENDING,
    ]);

    // Act - змінюємо статус платежу
    $payment->status = PaymentStatus::SUCCESS;
    $payment->save();

    // Оновлюємо модель з бази даних
    $payment->refresh();

    // Assert - перевіряємо, що статус змінено
    expect($payment->status)->toBe(PaymentStatus::SUCCESS);

    // Перевіряємо, що зміни збережені в базі даних
    $this->assertDatabaseHas('payments', [
        'id' => $payment->id,
        'status' => PaymentStatus::SUCCESS->value,
    ]);
});
