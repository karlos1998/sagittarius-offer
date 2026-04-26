<?php

use App\Filament\Resources\Ammunitions\Pages\CreateAmmunition;
use App\Filament\Resources\Ammunitions\Pages\EditAmmunition;
use App\Models\Ammunition;
use App\Models\Caliber;
use App\Models\User;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Livewire\Livewire;

beforeEach(function () {
    $user = User::factory()->create();

    $user = new class($user->getAttributes()) extends User implements FilamentUser
    {
        public function canAccessPanel(Panel $panel): bool
        {
            return true;
        }
    };

    $user->exists = true;

    $this->actingAs($user);
});

it('creates ammunition from the filament resource form with cart quantity step', function () {
    $caliber = Caliber::factory()->create();

    Livewire::test(CreateAmmunition::class)
        ->fillForm([
            'name' => '9x19 FMJ',
            'caliber_id' => $caliber->id,
            'club_price' => 2.50,
            'standard_price' => 5.00,
            'cart_quantity_step' => 10,
        ])
        ->call('create')
        ->assertHasNoFormErrors()
        ->assertNotified()
        ->assertRedirect();

    $this->assertDatabaseHas(Ammunition::class, [
        'name' => '9x19 FMJ',
        'caliber_id' => $caliber->id,
        'cart_quantity_step' => 10,
    ]);
});

it('updates ammunition from the filament resource form with cart quantity step', function () {
    $caliber = Caliber::factory()->create();
    $ammunition = Ammunition::factory()->for($caliber)->create([
        'cart_quantity_step' => 5,
    ]);

    Livewire::test(EditAmmunition::class, ['record' => $ammunition->getRouteKey()])
        ->fillForm([
            'name' => '9x19 JHP',
            'caliber_id' => $caliber->id,
            'club_price' => 3.50,
            'standard_price' => 6.50,
            'cart_quantity_step' => 15,
        ])
        ->call('save')
        ->assertHasNoFormErrors()
        ->assertNotified();

    $ammunition->refresh();

    expect($ammunition->name)->toBe('9x19 JHP')
        ->and($ammunition->cart_quantity_step)->toBe(15);
});
