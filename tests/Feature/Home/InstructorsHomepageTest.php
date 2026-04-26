<?php

use App\Models\Instructor;

it('renders active instructors on the home page', function () {
    Instructor::factory()->create([
        'full_name' => 'Jan Kowalski',
        'description' => 'Instruktor prowadzący szkolenia podstawowe i dynamiczne.',
        'photo' => 'instructors/jan-kowalski.jpg',
        'sort_order' => 2,
        'is_active' => true,
    ]);

    Instructor::factory()->create([
        'full_name' => 'Adam Nieaktywny',
        'description' => 'Ten rekord nie powinien trafić na frontend.',
        'sort_order' => 1,
        'is_active' => false,
    ]);

    $response = $this->get(route('home'));

    $response->assertOk()
        ->assertViewHas('page');

    $page = $response->viewData('page');

    expect($page['component'])->toBe('Home/Index')
        ->and($page['props']['instructors'])->toHaveCount(1)
        ->and($page['props']['instructors'][0]['full_name'])->toBe('Jan Kowalski')
        ->and($page['props']['instructors'][0]['description'])->toBe('Instruktor prowadzący szkolenia podstawowe i dynamiczne.')
        ->and($page['props']['instructors'][0]['photo_url'])->toBe('/storage/instructors/jan-kowalski.jpg');
});
