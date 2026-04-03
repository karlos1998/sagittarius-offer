<?php

use App\Models\Ammunition;
use App\Models\Caliber;
use App\Models\Gun;
use App\Models\GunPackage;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;

beforeEach(function () {
    $this->withMiddleware();
    $this->withoutMiddleware([
        VerifyCsrfToken::class,
        ValidateCsrfToken::class,
    ]);
});

it('adds whole gun package to cart', function () {
    $firstCaliber = Caliber::factory()->create();
    $firstAmmunition = Ammunition::factory()->for($firstCaliber)->create();
    $firstGun = Gun::factory()->for($firstCaliber)->create();

    $secondCaliber = Caliber::factory()->create();
    $secondAmmunition = Ammunition::factory()->for($secondCaliber)->create();
    $secondGun = Gun::factory()->for($secondCaliber)->create();

    $package = GunPackage::factory()->create([
        'name' => 'Pakiet Military USA',
    ]);

    $package->packageGuns()->createMany([
        [
            'gun_id' => $firstGun->id,
            'ammunition_id' => $firstAmmunition->id,
            'shots_quantity' => 10,
            'sort_order' => 1,
        ],
        [
            'gun_id' => $secondGun->id,
            'ammunition_id' => $secondAmmunition->id,
            'shots_quantity' => 5,
            'sort_order' => 2,
        ],
    ]);

    $response = $this->post(route('cart.add-package'), [
        'package_id' => $package->id,
    ]);

    $response
        ->assertRedirect()
        ->assertSessionHas('success', 'Pakiet został dodany do koszyka');

    $cart = session('cart', []);

    expect($cart)->toHaveCount(2);
    expect($cart[$firstGun->id]['ammunitions'])->toBe([$firstAmmunition->id => 10]);
    expect($cart[$secondGun->id]['ammunitions'])->toBe([$secondAmmunition->id => 5]);
    expect($cart[$firstGun->id]['package_name'])->toBe('Pakiet Military USA');
    expect($cart[$secondGun->id]['package_name'])->toBe('Pakiet Military USA');
    expect($cart[$firstGun->id]['package_guns'])->toContain(
        $firstGun->name.' - 10 strzałów ('.$firstAmmunition->name.')',
        $secondGun->name.' - 5 strzałów ('.$secondAmmunition->name.')'
    );
});

it('removes gun from cart without ammo id', function () {
    $caliber = Caliber::factory()->create();
    Ammunition::factory()->for($caliber)->create();
    $gun = Gun::factory()->for($caliber)->create();

    $this->post(route('cart.add'), [
        'gun_id' => $gun->id,
    ])->assertRedirect();

    $this->post(route('cart.update'), [
        'gun_id' => $gun->id,
        'action' => 'remove',
    ])
        ->assertRedirect()
        ->assertSessionHas('success', 'Koszyk został zaktualizowany');

    expect(session('cart', []))->toBe([]);
});
