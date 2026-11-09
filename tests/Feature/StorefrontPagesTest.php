<?php

use App\Models\Ammunition;
use App\Models\Caliber;
use App\Models\Gun;
use App\Models\GunPackage;
use Inertia\Testing\AssertableInertia;

it('renders home page', function () {
    $this->get(route('home'))->assertOk();
});

it('renders guns offer page', function () {
    $caliber = Caliber::factory()->create();
    Ammunition::factory()->for($caliber)->create([
        'cart_quantity_step' => 10,
    ]);
    Gun::factory()->for($caliber)->create();

    $this->get(route('guns.index'))
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Guns/Index')
            ->has('guns', 1)
            ->where('guns.0.caliber.ammunitions.0.cart_quantity_step', 10)
        );
});

it('shares club member status and package ammunition prices on the guns offer page', function () {
    $caliber = Caliber::factory()->create();
    $ammunition = Ammunition::factory()->for($caliber)->create([
        'standard_price' => 3.0,
        'club_price' => 2.0,
    ]);
    $gun = Gun::factory()->for($caliber)->create();
    $package = GunPackage::factory()->create();
    $package->packageGuns()->create([
        'gun_id' => $gun->id,
        'ammunition_id' => $ammunition->id,
        'shots_quantity' => 10,
        'sort_order' => 1,
    ]);

    $this->withSession(['is_club_member' => true])
        ->get(route('guns.index'))
        ->assertSuccessful()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->where('isClubMember', true)
            ->where('gunPackages.0.package_guns.0.ammunition.standard_price', 3)
            ->where('gunPackages.0.package_guns.0.ammunition.club_price', 2)
        );
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
