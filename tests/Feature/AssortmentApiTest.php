<?php

use App\Models\Ammunition;
use App\Models\Caliber;
use App\Models\Gun;
use App\Models\GunPackage;
use App\Models\GunType;
use App\Models\User;

beforeEach(function (): void {
    $this->admin = User::factory()->create();
    config(['assortment.email' => $this->admin->email]);
    $this->credentials = ['email' => $this->admin->email, 'password' => 'password'];
    $this->gunRow = ['name' => 'Test gun', 'gun_type' => 'Pistol', 'caliber' => '9 mm'];
});

it('requires valid credentials for both endpoints', function (string $endpoint): void {
    $this->postJson($endpoint)->assertUnauthorized();
    $this->postJson($endpoint, [...$this->credentials, 'password' => 'wrong'])->assertUnauthorized();
    $this->postJson($endpoint, [...$this->credentials, 'password' => ['password']])->assertUnauthorized();
    $this->postJson($endpoint.'?'.http_build_query($this->credentials))->assertUnauthorized();
})->with(['/api/assortment', '/api/assortment/import']);

it('rejects other registered users and disabled access', function (): void {
    $other = User::factory()->create();
    $this->postJson('/api/assortment', ['email' => $other->email, 'password' => 'password'])->assertUnauthorized();
    config(['assortment.email' => null]);
    $this->postJson('/api/assortment', $this->credentials)->assertUnauthorized();
});

it('lists the current assortment with IDs and relationships', function (): void {
    $gun = Gun::factory()->create();
    $ammunition = Ammunition::factory()->create();

    $this->postJson('/api/assortment', $this->credentials)->assertOk()
        ->assertJsonPath('guns.0.id', $gun->id)
        ->assertJsonPath('guns.0.caliber.name', $gun->caliber->name)
        ->assertJsonPath('ammunitions.0.id', $ammunition->id)
        ->assertJsonMissingPath('password');
});

it('creates guns and ammunition with shared dictionaries', function (): void {
    $this->postJson('/api/assortment/import', [
        ...$this->credentials,
        'guns' => [$this->gunRow],
        'ammunitions' => [[
            'name' => 'Test ammunition', 'caliber' => '9 mm',
            'club_price' => '2.50', 'standard_price' => '3.00', 'cart_quantity_step' => 5,
        ]],
    ])->assertOk()->assertJsonPath('guns.0.name', 'Test gun')
        ->assertJsonPath('ammunitions.0.standard_price', '3.00');

    expect(Gun::query()->count())->toBe(1);
    expect(Caliber::query()->count())->toBe(1);
    expect(GunType::query()->count())->toBe(1);
    expect(Ammunition::query()->first()->cart_quantity_step)->toBe(5);
});

it('updates by ID without changing photos descriptions or package membership', function (): void {
    $gun = Gun::factory()->create(['photos' => ['guns/photo.jpg'], 'description' => 'Keep this']);
    $untouched = Gun::factory()->create();
    $package = GunPackage::factory()->create();
    $ammunition = Ammunition::factory()->create(['caliber_id' => $gun->caliber_id]);
    $package->guns()->attach($gun, ['ammunition_id' => $ammunition->id, 'shots_quantity' => 10, 'sort_order' => 0]);
    $payload = [...$this->credentials, 'guns' => [[
        'id' => $gun->id, 'name' => 'Updated', 'caliber' => $gun->caliber->name, 'gun_type' => $gun->gunType->name,
    ]]];

    $this->postJson('/api/assortment/import', $payload)->assertOk();
    $this->postJson('/api/assortment/import', $payload)->assertOk();

    expect($gun->fresh()->name)->toBe('Updated');
    expect($gun->fresh()->description)->toBe('Keep this');
    expect($gun->fresh()->photos)->toBe(['guns/photo.jpg']);
    expect($gun->packages()->first()->pivot->shots_quantity)->toBe(10);
    expect($untouched->fresh()->name)->toBe($untouched->name);
    expect(Gun::query()->count())->toBe(2);
});

it('updates ammunition prices while preserving omitted values', function (): void {
    $ammunition = Ammunition::factory()->create(['club_price' => '2.50', 'cart_quantity_step' => 5]);
    $this->postJson('/api/assortment/import', [
        ...$this->credentials,
        'ammunitions' => [[
            'id' => $ammunition->id, 'name' => $ammunition->name,
            'caliber' => $ammunition->caliber->name, 'standard_price' => '4.50',
        ]],
    ])->assertOk();

    expect($ammunition->fresh()->standard_price)->toBe('4.50');
    expect($ammunition->fresh()->club_price)->toBe('2.50');
    expect($ammunition->fresh()->cart_quantity_step)->toBe(5);
});

it('rejects invalid batches before saving anything', function (): void {
    $this->postJson('/api/assortment/import', [
        ...$this->credentials,
        'guns' => [$this->gunRow, [...$this->gunRow, 'id' => 999999]],
    ])->assertUnprocessable()->assertJsonValidationErrors('guns.1.id');

    $this->postJson('/api/assortment/import', [
        ...$this->credentials,
        'guns' => [$this->gunRow],
        'ammunitions' => [['name' => 'Invalid', 'caliber' => '9 mm', 'standard_price' => -1]],
    ])->assertUnprocessable()->assertJsonValidationErrors('ammunitions.0.standard_price');

    expect(Gun::query()->count())->toBe(0);
    expect(Caliber::query()->count())->toBe(0);
});

it('rejects repeated IDs', function (): void {
    $gun = Gun::factory()->create();
    $row = [...$this->gunRow, 'id' => $gun->id];
    $this->postJson('/api/assortment/import', [
        ...$this->credentials, 'guns' => [$row, $row],
    ])->assertUnprocessable()->assertJsonValidationErrors('guns.0.id');
});

it('rolls back the whole batch when a later save fails', function (): void {
    Ammunition::saving(function (): void {
        throw new RuntimeException('Simulated failure');
    });

    try {
        $this->postJson('/api/assortment/import', [
            ...$this->credentials,
            'guns' => [$this->gunRow],
            'ammunitions' => [['name' => 'Fail', 'caliber' => '9 mm']],
        ])->assertServerError();
        expect(Gun::query()->count())->toBe(0);
        expect(Caliber::query()->count())->toBe(0);
        expect(GunType::query()->count())->toBe(0);
    } finally {
        Ammunition::flushEventListeners();
    }
});

it('limits authentication attempts', function (): void {
    for ($attempt = 0; $attempt < 10; $attempt++) {
        $this->postJson('/api/assortment', ['email' => 'invalid', 'password' => 'invalid'])->assertUnauthorized();
    }
    $this->postJson('/api/assortment', $this->credentials)->assertTooManyRequests();
});
