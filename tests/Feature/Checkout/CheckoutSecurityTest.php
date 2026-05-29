<?php

use App\Enums\OrderPaymentMethod;
use App\Enums\OrderPaymentStatus;
use App\Enums\OrderStatus;
use App\Mail\OrderVerificationCodeMail;
use App\Models\Ammunition;
use App\Models\Caliber;
use App\Models\Gun;
use App\Models\Order;
use App\Services\TurnstileService;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

beforeEach(function () {
    $this->withMiddleware();
    $this->withoutMiddleware([
        VerifyCsrfToken::class,
        ValidateCsrfToken::class,
    ]);

    config([
        'services.turnstile.enabled' => false,
        'services.turnstile.site_key' => null,
        'services.turnstile.secret_key' => null,
    ]);
});

it('limits checkout email attempts with Polish feedback', function () {
    Mail::fake();

    config([
        'checkout.require_voucher_email_verification' => true,
    ]);

    $session = checkoutSecurityCartSession();
    $payload = checkoutSecurityPayload([
        'email' => 'limit@example.com',
    ]);

    for ($attempt = 1; $attempt <= 5; $attempt++) {
        $this
            ->withSession($session)
            ->from(route('checkout.index'))
            ->post(route('checkout.store'), $payload)
            ->assertRedirect();
    }

    $this
        ->withSession($session)
        ->from(route('checkout.index'))
        ->post(route('checkout.store'), $payload)
        ->assertRedirect(route('checkout.index'))
        ->assertSessionHas('error', 'Za dużo prób. Spróbuj ponownie za chwilę.');

    expect(Order::query()->count())->toBe(5);

    Mail::assertQueued(OrderVerificationCodeMail::class, 5);
});

it('limits verification code attempts with Polish feedback', function () {
    $order = Order::factory()->create([
        'status' => OrderStatus::PendingVerification,
        'payment_method' => OrderPaymentMethod::PayOnSite,
        'payment_status' => OrderPaymentStatus::Pending,
        'verification_code_hash' => Hash::make('123456'),
        'verification_code_expires_at' => now()->addMinutes(5),
    ]);

    for ($attempt = 1; $attempt <= 5; $attempt++) {
        $this
            ->withSession([
                'checkout_order_id' => $order->id,
            ])
            ->from(route('checkout.show', ['order' => $order->public_id]))
            ->post(route('checkout.verify', ['order' => $order->public_id]), [
                'code' => '000000',
            ])
            ->assertSessionHasErrors('code');
    }

    $this
        ->withSession([
            'checkout_order_id' => $order->id,
        ])
        ->from(route('checkout.show', ['order' => $order->public_id]))
        ->post(route('checkout.verify', ['order' => $order->public_id]), [
            'code' => '000000',
        ])
        ->assertRedirect(route('checkout.show', ['order' => $order->public_id]))
        ->assertSessionHas('error', 'Za dużo prób. Spróbuj ponownie za chwilę.');
});

it('rejects checkout submissions when turnstile verification fails', function () {
    Mail::fake();
    Http::fake([
        TurnstileService::SITEVERIFY_URL => Http::response([
            'success' => false,
            'error-codes' => ['invalid-input-response'],
        ]),
    ]);

    config([
        'services.turnstile.enabled' => true,
        'services.turnstile.site_key' => 'site-key',
        'services.turnstile.secret_key' => 'secret-key',
    ]);

    $this
        ->withSession(checkoutSecurityCartSession())
        ->from(route('checkout.index'))
        ->post(route('checkout.store'), checkoutSecurityPayload([
            'turnstile_token' => 'invalid-token',
        ]))
        ->assertRedirect(route('checkout.index'))
        ->assertSessionHasErrors('turnstile_token');

    expect(Order::query()->count())->toBe(0);

    Http::assertSent(fn ($request): bool => $request->url() === TurnstileService::SITEVERIFY_URL
        && $request['secret'] === 'secret-key'
        && $request['response'] === 'invalid-token');
});

it('rejects verification code submissions when turnstile verification fails', function () {
    Http::fake([
        TurnstileService::SITEVERIFY_URL => Http::response([
            'success' => false,
            'error-codes' => ['invalid-input-response'],
        ]),
    ]);

    config([
        'services.turnstile.enabled' => true,
        'services.turnstile.site_key' => 'site-key',
        'services.turnstile.secret_key' => 'secret-key',
    ]);

    $order = Order::factory()->create([
        'status' => OrderStatus::PendingVerification,
        'payment_method' => OrderPaymentMethod::PayOnSite,
        'payment_status' => OrderPaymentStatus::Pending,
        'verification_code_hash' => Hash::make('123456'),
        'verification_code_expires_at' => now()->addMinutes(5),
        'verified_at' => null,
    ]);

    $this
        ->withSession([
            'checkout_order_id' => $order->id,
        ])
        ->from(route('checkout.show', ['order' => $order->public_id]))
        ->post(route('checkout.verify', ['order' => $order->public_id]), [
            'code' => '123456',
            'turnstile_token' => 'invalid-token',
        ])
        ->assertRedirect(route('checkout.show', ['order' => $order->public_id]))
        ->assertSessionHasErrors('turnstile_token');

    expect($order->refresh()->verified_at)->toBeNull();
});

it('rejects resend code requests when turnstile verification fails', function () {
    Mail::fake();
    Http::fake([
        TurnstileService::SITEVERIFY_URL => Http::response([
            'success' => false,
            'error-codes' => ['invalid-input-response'],
        ]),
    ]);

    config([
        'services.turnstile.enabled' => true,
        'services.turnstile.site_key' => 'site-key',
        'services.turnstile.secret_key' => 'secret-key',
    ]);

    $order = Order::factory()->create([
        'status' => OrderStatus::PendingVerification,
        'verification_code_hash' => Hash::make('123456'),
        'verification_code_expires_at' => now()->subMinute(),
    ]);

    $this
        ->withSession([
            'checkout_order_id' => $order->id,
        ])
        ->from(route('checkout.show', ['order' => $order->public_id]))
        ->post(route('checkout.resend-code', ['order' => $order->public_id]), [
            'turnstile_token' => 'invalid-token',
        ])
        ->assertRedirect(route('checkout.show', ['order' => $order->public_id]))
        ->assertSessionHasErrors('turnstile_token');

    Mail::assertNotQueued(OrderVerificationCodeMail::class);
});

/**
 * @return array<string, mixed>
 */
function checkoutSecurityCartSession(): array
{
    $caliber = Caliber::factory()->create();
    $ammunition = Ammunition::factory()->for($caliber)->create([
        'club_price' => 2.50,
        'standard_price' => 5.00,
    ]);
    $gun = Gun::factory()->for($caliber)->create();

    return [
        'cart' => [
            $gun->id => [
                'gun_id' => $gun->id,
                'ammunitions' => [
                    $ammunition->id => 10,
                ],
            ],
        ],
        'is_club_member' => false,
    ];
}

/**
 * @param  array<string, mixed>  $overrides
 * @return array<string, mixed>
 */
function checkoutSecurityPayload(array $overrides = []): array
{
    return [
        'first_name' => 'Jan',
        'last_name' => 'Kowalski',
        'street' => 'Testowa',
        'house_number' => '1A',
        'apartment_number' => '12',
        'postal_code' => '30-001',
        'city' => 'Kraków',
        'email' => 'jan@example.com',
        'payment_method' => OrderPaymentMethod::PayOnSite->value,
        ...$overrides,
    ];
}
