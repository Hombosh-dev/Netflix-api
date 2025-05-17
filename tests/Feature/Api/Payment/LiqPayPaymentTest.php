<?php

namespace Tests\Feature\Api\Payment;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Models\Tariff;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class LiqPayPaymentTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        
        // Set up LiqPay config for testing
        Config::set('services.liqpay.public_key', 'test_public_key');
        Config::set('services.liqpay.private_key', 'test_private_key');
    }

    /** @test */
    public function it_redirects_to_liqpay_when_payment_method_is_liqpay()
    {
        // Arrange
        $user = User::factory()->create();
        $tariff = Tariff::factory()->create([
            'price' => 199.99,
            'currency' => 'UAH',
            'duration_days' => 30,
            'is_active' => true
        ]);
        
        // Act
        $response = $this->actingAs($user)
            ->postJson('/api/v1/payments', [
                'tariff_id' => $tariff->id,
                'amount' => 199.99,
                'currency' => 'UAH',
                'payment_method' => 'LiqPay'
            ]);
        
        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'redirect_url',
                'message'
            ]);
        
        $this->assertStringContainsString('/api/v1/liqpay/create-payment', $response->json('redirect_url'));
    }

    /** @test */
    public function it_creates_payment_with_liqpay_controller()
    {
        // Arrange
        $user = User::factory()->create();
        $tariff = Tariff::factory()->create([
            'price' => 199.99,
            'currency' => 'UAH',
            'duration_days' => 30,
            'is_active' => true
        ]);
        
        // Act
        $response = $this->actingAs($user)
            ->post('/api/v1/liqpay/create-payment', [
                'tariff_id' => $tariff->id
            ]);
        
        // Assert
        $response->assertStatus(302); // Redirect to LiqPay
        $this->assertStringContainsString('liqpay.ua', $response->headers->get('Location'));
    }

    /** @test */
    public function it_processes_liqpay_callback_successfully()
    {
        // Arrange
        $user = User::factory()->create();
        $tariff = Tariff::factory()->create([
            'price' => 199.99,
            'currency' => 'UAH',
            'duration_days' => 30,
            'is_active' => true
        ]);
        
        // Create a payment record
        $payment = Payment::factory()->create([
            'user_id' => $user->id,
            'tariff_id' => $tariff->id,
            'amount' => 199.99,
            'currency' => 'UAH',
            'payment_method' => 'LiqPay',
            'transaction_id' => 'test_order_id',
            'status' => PaymentStatus::PENDING
        ]);
        
        // Mock LiqPay callback data
        $paymentData = [
            'action' => 'pay',
            'payment_id' => 123456,
            'status' => 'success',
            'version' => 3,
            'type' => 'buy',
            'paytype' => 'card',
            'public_key' => 'test_public_key',
            'acq_id' => 123456,
            'order_id' => 'test_order_id',
            'liqpay_order_id' => 'ABCDEF123456',
            'description' => 'Test payment',
            'sender_phone' => '+380991234567',
            'sender_card_mask2' => '123456****1234',
            'sender_card_bank' => 'Test Bank',
            'sender_card_type' => 'visa',
            'sender_card_country' => 804,
            'amount' => 199.99,
            'currency' => 'UAH',
            'sender_commission' => 0,
            'receiver_commission' => 0,
            'agent_commission' => 0,
            'amount_debit' => 199.99,
            'amount_credit' => 199.99,
            'commission_debit' => 0,
            'commission_credit' => 0,
            'currency_debit' => 'UAH',
            'currency_credit' => 'UAH',
            'sender_bonus' => 0,
            'amount_bonus' => 0,
            'mpi_eci' => '7',
            'is_3ds' => false,
            'create_date' => time(),
            'end_date' => time(),
            'transaction_id' => 123456
        ];
        
        $data = base64_encode(json_encode($paymentData));
        
        // Mock signature verification by using a fixed signature
        $signature = 'test_signature';
        
        // Act
        $response = $this->postJson('/api/v1/liqpay/callback', [
            'data' => $data,
            'signature' => $signature
        ]);
        
        // Assert
        $response->assertStatus(200);
        
        // Refresh the payment from the database
        $payment->refresh();
        
        // Check that the payment status was updated
        $this->assertEquals(PaymentStatus::SUCCESS, $payment->status);
        
        // Check that a subscription was created
        $subscription = UserSubscription::where('user_id', $user->id)
            ->where('tariff_id', $tariff->id)
            ->first();
            
        $this->assertNotNull($subscription);
        $this->assertTrue($subscription->is_active);
        $this->assertEquals(now()->addDays(30)->format('Y-m-d'), $subscription->end_date->format('Y-m-d'));
    }
}
