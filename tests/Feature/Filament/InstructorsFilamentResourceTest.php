<?php

use App\Filament\Resources\Instructors\Pages\CreateInstructor;
use App\Filament\Resources\Instructors\Pages\EditInstructor;
use App\Models\Instructor;
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

it('creates an instructor from the filament resource form', function () {
    Livewire::test(CreateInstructor::class)
        ->fillForm([
            'full_name' => 'Anna Nowak',
            'description' => 'Specjalistka od szkoleń dla początkujących.',
            'sort_order' => 3,
            'is_active' => true,
            'photo' => 'instructors/anna-nowak.jpg',
        ])
        ->call('create')
        ->assertHasNoFormErrors()
        ->assertNotified()
        ->assertRedirect();

    $this->assertDatabaseHas(Instructor::class, [
        'full_name' => 'Anna Nowak',
        'description' => 'Specjalistka od szkoleń dla początkujących.',
        'sort_order' => 3,
        'is_active' => true,
        'photo' => 'instructors/anna-nowak.jpg',
    ]);
});

it('updates an instructor from the filament resource form', function () {
    $instructor = Instructor::factory()->create([
        'full_name' => 'Piotr Stary',
        'description' => 'Stary opis',
        'sort_order' => 5,
        'is_active' => true,
    ]);

    Livewire::test(EditInstructor::class, ['record' => $instructor->getRouteKey()])
        ->fillForm([
            'full_name' => 'Piotr Nowy',
            'description' => 'Nowy opis instruktora.',
            'sort_order' => 1,
            'is_active' => false,
            'photo' => 'instructors/piotr-nowy.jpg',
        ])
        ->call('save')
        ->assertHasNoFormErrors()
        ->assertNotified();

    $instructor->refresh();

    expect($instructor->full_name)->toBe('Piotr Nowy')
        ->and($instructor->description)->toBe('Nowy opis instruktora.')
        ->and($instructor->sort_order)->toBe(1)
        ->and($instructor->is_active)->toBeFalse()
        ->and($instructor->photo)->toBe('instructors/piotr-nowy.jpg');
});
