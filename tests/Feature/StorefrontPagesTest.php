<?php

use App\Models\Ammunition;
use App\Models\Caliber;
use App\Models\Gun;

it('renders home page', function () {
    $this->get(route('home'))->assertOk();
});

it('renders guns offer page', function () {
    $this->get(route('guns.index'))->assertOk();
});

it('renders cart page with cart data', function () {
    $caliber = Caliber::factory()->create();
    $ammunition = Ammunition::factory()->for($caliber)->create();
    $gun = Gun::factory()->for($caliber)->create();

    $this
        ->withSession([
            'cart' => [
                $gun->id => [
                    'gun_id' => $gun->id,
                    'ammunitions' => [
                        $ammunition->id => 5,
                    ],
                ],
            ],
        ])
        ->get(route('cart.index'))
        ->assertOk();
});

it('stores club member status in session', function () {
    $caliber = Caliber::factory()->create();
    $ammunition = Ammunition::factory()->for($caliber)->create();
    $gun = Gun::factory()->for($caliber)->create();

    $this
        ->withSession([
            'cart' => [
                $gun->id => [
                    'gun_id' => $gun->id,
                    'ammunitions' => [
                        $ammunition->id => 5,
                    ],
                ],
            ],
            'is_club_member' => true,
        ])
        ->post(route('cart.toggle-club-member'), [
            'is_club_member' => true,
        ])
        ->assertRedirect()
        ->assertSessionHas('is_club_member', true);
});

it('renders checkout page when cart has items', function () {
    $caliber = Caliber::factory()->create();
    $ammunition = Ammunition::factory()->for($caliber)->create();
    $gun = Gun::factory()->for($caliber)->create();

    $this
        ->withSession([
            'cart' => [
                $gun->id => [
                    'gun_id' => $gun->id,
                    'ammunitions' => [
                        $ammunition->id => 5,
                    ],
                ],
            ],
            'is_club_member' => false,
        ])
        ->get(route('checkout.index'))
        ->assertOk();
});
