<?php

use App\Enums\OrderPaymentStatus;
use App\Models\Order;
use App\Models\User;

it('redirects guests from the employee panel to login', function () {
    $this->get(route('panel.index'))
        ->assertRedirect(route('login'));
});

it('shows latest orders first in the employee panel', function () {
    $user = User::factory()->create();

    $olderOrder = Order::factory()->create([
        'order_number' => 'SO-OLDER-001',
        'first_name' => 'Anna',
        'last_name' => 'Nowak',
        'email' => 'anna@example.com',
        'created_at' => now()->subDay(),
    ]);

    $newerOrder = Order::factory()->create([
        'order_number' => 'SO-NEWER-001',
        'first_name' => 'Jan',
        'last_name' => 'Kowalski',
        'email' => 'jan@example.com',
        'created_at' => now(),
    ]);

    $response = $this
        ->actingAs($user)
        ->get(route('panel.index'));

    $response
        ->assertOk()
        ->assertSeeInOrder([
            $newerOrder->order_number,
            $olderOrder->order_number,
        ]);
});

it('filters orders by search phrase in the employee panel', function () {
    $user = User::factory()->create();

    Order::factory()->create([
        'order_number' => 'SO-FIND-001',
        'first_name' => 'Karol',
        'last_name' => 'Tester',
        'email' => 'karol@example.com',
    ]);

    Order::factory()->create([
        'order_number' => 'SO-HIDE-001',
        'first_name' => 'Anna',
        'last_name' => 'Ukryta',
        'email' => 'anna@example.com',
    ]);

    $this
        ->actingAs($user)
        ->get(route('panel.index', ['search' => 'karol@example.com']))
        ->assertOk()
        ->assertSee('SO-FIND-001')
        ->assertDontSee('SO-HIDE-001');
});

it('marks an order as completed from the employee panel', function () {
    $user = User::factory()->create();

    $order = Order::factory()->create([
        'order_number' => 'SO-COMPLETE-001',
        'is_completed' => false,
        'completed_at' => null,
    ]);

    $this
        ->actingAs($user)
        ->post(route('panel.orders.complete', $order))
        ->assertRedirect(route('panel.index'));

    $order->refresh();

    expect($order->is_completed)->toBeTrue()
        ->and($order->completed_at)->not->toBeNull();
});

it('marks an order as completed and paid from the employee panel', function () {
    $user = User::factory()->create();

    $order = Order::factory()->create([
        'order_number' => 'SO-COMPLETE-PAID-001',
        'is_completed' => false,
        'completed_at' => null,
    ]);

    $this
        ->actingAs($user)
        ->post(route('panel.orders.complete', $order), [
            'mark_as_paid' => true,
        ])
        ->assertRedirect(route('panel.index'));

    $order->refresh();

    expect($order->is_completed)->toBeTrue()
        ->and($order->completed_at)->not->toBeNull()
        ->and($order->payment_status)->toBe(OrderPaymentStatus::Paid)
        ->and($order->paid_at)->not->toBeNull();
});
