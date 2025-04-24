<?php

use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;

uses(RefreshDatabase::class);

test('users can verify their email through api', function () {
    $user = User::factory()->unverified()->create();
    
    Event::fake();
    
    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1($user->email)]
    );
    
    $response = $this->actingAs($user)->get($verificationUrl);
    
    $response->assertRedirect(config('app.frontend_url').'/?verified=1');
    
    expect($user->fresh()->hasVerifiedEmail())->toBeTrue();
    Event::assertDispatched(Verified::class);
});

test('users cannot verify with invalid signature', function () {
    $user = User::factory()->unverified()->create();
    
    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1($user->email)]
    );
    
    // Invalidate the signature by adding a character
    $invalidUrl = $verificationUrl . 'a';
    
    $response = $this->actingAs($user)->get($invalidUrl);
    
    $response->assertStatus(403);
    expect($user->fresh()->hasVerifiedEmail())->toBeFalse();
});

test('users cannot verify with invalid hash', function () {
    $user = User::factory()->unverified()->create();
    
    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1('wrong-email')]
    );
    
    $response = $this->actingAs($user)->get($verificationUrl);
    
    $response->assertStatus(403);
    expect($user->fresh()->hasVerifiedEmail())->toBeFalse();
});

test('users can resend verification email', function () {
    Notification::fake();
    
    $user = User::factory()->unverified()->create();
    
    $response = $this->actingAs($user)
        ->postJson('/api/v1/email/verification-notification');
    
    $response->assertStatus(200)
        ->assertJson(['status' => 'verification-link-sent']);
    
    Notification::assertSentTo($user, VerifyEmail::class);
});

test('verified users cannot resend verification email', function () {
    Notification::fake();
    
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);
    
    $response = $this->actingAs($user)
        ->postJson('/api/v1/email/verification-notification');
    
    $response->assertRedirect('/');
    
    Notification::assertNotSentTo($user, VerifyEmail::class);
});
