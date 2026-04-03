<?php

use App\Enums\OrderPaymentMethod;
use App\Enums\OrderPaymentStatus;
use App\Enums\OrderStatus;
use App\Mail\OrderConfirmedMail;
use App\Mail\OrderVerificationCodeMail;
use App\Models\Ammunition;
use App\Models\Caliber;
use App\Models\Gun;
use App\Models\GunPackage;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\OrderPdfGenerator;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Mockery\MockInterface;

beforeEach(function () {
    $this->withMiddleware();
    $this->withoutMiddleware([
        VerifyCsrfToken::class,
        ValidateCsrfToken::class,
    ]);

    $this->mock(OrderPdfGenerator::class, function (MockInterface $mock): void {
        $mock->shouldReceive('generate')
            ->andReturn('%PDF-1.4 fake');
    });
});

it('creates order and sends verification code email', function () {
    Mail::fake();

    $caliber = Caliber::factory()->create();
    $ammunition = Ammunition::factory()->for($caliber)->create([
        'club_price' => 2.50,
        'standard_price' => 5.00,
    ]);
    $gun = Gun::factory()->for($caliber)->create();

    $response = $this
        ->withSession([
            'cart' => [
                $gun->id => [
                    'gun_id' => $gun->id,
                    'ammunitions' => [
                        $ammunition->id => 10,
                    ],
                ],
            ],
            'is_club_member' => false,
        ])
        ->post(route('checkout.store'), [
            'first_name' => 'Jan',
            'last_name' => 'Kowalski',
            'street' => 'Testowa',
            'house_number' => '1A',
            'apartment_number' => '12',
            'postal_code' => '30-001',
            'city' => 'Kraków',
            'email' => 'jan@example.com',
            'payment_method' => OrderPaymentMethod::PayOnSite->value,
        ]);

    $response->assertRedirect(route('checkout.index'));

    $order = Order::query()->first();

    expect($order)->not->toBeNull();
    expect($order->status)->toBe(OrderStatus::PendingVerification);
    expect($order->payment_status)->toBe(OrderPaymentStatus::Pending);
    expect((float) $order->total_amount)->toBe(50.0);
    expect($order->total_shots)->toBe(10);
    expect($order->verification_code_hash)->not->toBeNull();
    expect($order->street)->toBe('Testowa');
    expect($order->house_number)->toBe('1A');
    expect($order->apartment_number)->toBe('12');
    expect($order->postal_code)->toBe('30-001');
    expect($order->city)->toBe('Kraków');
    expect($order->verification_code_expires_at?->isAfter(now()->addMinutes(4)))->toBeTrue();

    expect(OrderItem::query()->count())->toBe(1);

    Mail::assertSent(OrderVerificationCodeMail::class, function (OrderVerificationCodeMail $mail) use ($order): bool {
        return $mail->hasTo($order->email)
            && $mail->order->is($order)
            && strlen($mail->verificationCode) === 6;
    });
});

it('stores package details on order items when gun comes from package', function () {
    Mail::fake();

    $caliber = Caliber::factory()->create();
    $ammunition = Ammunition::factory()->for($caliber)->create([
        'club_price' => 2.50,
        'standard_price' => 5.00,
    ]);
    $gun = Gun::factory()->for($caliber)->create([
        'name' => 'AR-15',
    ]);
    $secondGun = Gun::factory()->for($caliber)->create([
        'name' => 'Mossberg 500',
    ]);

    $package = GunPackage::factory()->create([
        'name' => 'Pakiet Military USA',
    ]);

    $package->packageGuns()->createMany([
        [
            'gun_id' => $gun->id,
            'ammunition_id' => $ammunition->id,
            'shots_quantity' => 10,
            'sort_order' => 1,
        ],
        [
            'gun_id' => $secondGun->id,
            'ammunition_id' => $ammunition->id,
            'shots_quantity' => 5,
            'sort_order' => 2,
        ],
    ]);

    $this
        ->withSession([
            'cart' => [
                $gun->id => [
                    'gun_id' => $gun->id,
                    'ammunitions' => [
                        $ammunition->id => 10,
                    ],
                    'package_id' => $package->id,
                    'package_name' => $package->name,
                    'package_guns' => [
                        $gun->name.' - 10 strzałów ('.$ammunition->name.')',
                        $secondGun->name.' - 5 strzałów ('.$ammunition->name.')',
                    ],
                ],
            ],
            'is_club_member' => false,
        ])
        ->post(route('checkout.store'), [
            'first_name' => 'Jan',
            'last_name' => 'Kowalski',
            'street' => 'Testowa',
            'house_number' => '1A',
            'apartment_number' => '12',
            'postal_code' => '30-001',
            'city' => 'Kraków',
            'email' => 'jan@example.com',
            'payment_method' => OrderPaymentMethod::PayOnSite->value,
        ])
        ->assertRedirect(route('checkout.index'));

    $orderItem = OrderItem::query()->first();

    expect($orderItem)->not->toBeNull();
    expect($orderItem->gun_package_id)->toBe($package->id);
    expect($orderItem->gun_package_name)->toBe('Pakiet Military USA');
    expect($orderItem->gun_package_guns_summary)->toContain('AR-15', 'Mossberg 500');
});

it('verifies order with code, clears cart, and sends pdf email', function () {
    Mail::fake();

    $order = Order::factory()->create([
        'status' => OrderStatus::PendingVerification,
        'payment_method' => OrderPaymentMethod::PayOnSite,
        'payment_status' => OrderPaymentStatus::Pending,
        'verification_code_hash' => Hash::make('123456'),
        'verification_code_expires_at' => now()->addMinutes(5),
        'download_token' => null,
    ]);

    OrderItem::factory()->for($order)->create([
        'gun_name' => 'Glock 17',
        'ammunition_name' => '9x19 FMJ',
    ]);

    $response = $this
        ->withSession([
            'checkout_order_id' => $order->id,
            'cart' => ['test' => ['gun_id' => 1, 'ammunitions' => [1 => 5]]],
            'is_club_member' => true,
        ])
        ->post(route('checkout.verify'), [
            'code' => '123456',
        ]);

    $response
        ->assertRedirect(route('checkout.index'))
        ->assertSessionMissing('cart')
        ->assertSessionMissing('is_club_member');

    $order->refresh();

    expect($order->status)->toBe(OrderStatus::Confirmed);
    expect($order->verified_at)->not->toBeNull();
    expect($order->download_token)->not->toBeNull();
    expect($order->verification_code_hash)->toBeNull();

    Mail::assertSent(OrderConfirmedMail::class, function (OrderConfirmedMail $mail) use ($order): bool {
        return $mail->hasTo($order->email)
            && $mail->order->is($order)
            && count($mail->attachments()) === 1;
    });
});

it('resends a different verification code and extends validity to 5 minutes', function () {
    Mail::fake();

    $oldCode = '123456';

    $order = Order::factory()->create([
        'status' => OrderStatus::PendingVerification,
        'verification_code_hash' => Hash::make($oldCode),
        'verification_code_expires_at' => now()->subMinute(),
    ]);

    $oldHash = $order->verification_code_hash;

    $response = $this
        ->withSession([
            'checkout_order_id' => $order->id,
        ])
        ->post(route('checkout.resend-code'));

    $response->assertRedirect(route('checkout.index'));

    $order->refresh();

    expect($order->verification_code_hash)->not->toBe($oldHash);
    expect(Hash::check($oldCode, (string) $order->verification_code_hash))->toBeFalse();
    expect($order->verification_code_expires_at?->isAfter(now()->addMinutes(4)))->toBeTrue();

    Mail::assertSent(OrderVerificationCodeMail::class, function (OrderVerificationCodeMail $mail) use ($order, $oldCode): bool {
        return $mail->hasTo($order->email)
            && $mail->order->is($order)
            && $mail->verificationCode !== $oldCode;
    });
});

it('allows downloading pdf only with valid token', function () {
    $order = Order::factory()->create([
        'status' => OrderStatus::Confirmed,
        'verified_at' => now(),
        'download_token' => 'valid-token',
    ]);

    OrderItem::factory()->for($order)->create();

    $this
        ->get(route('orders.download-pdf', ['order' => $order, 'token' => 'invalid-token']))
        ->assertForbidden();

    $this
        ->get(route('orders.download-pdf', ['order' => $order, 'token' => 'valid-token']))
        ->assertSuccessful()
        ->assertHeader('content-type', 'application/pdf');
});
