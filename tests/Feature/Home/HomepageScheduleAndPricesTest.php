<?php

use App\Models\Ammunition;
use App\Models\Caliber;
use App\Models\Gun;
use App\Models\GunType;
use Carbon\CarbonImmutable;
use Inertia\Testing\AssertableInertia;

afterEach(function () {
    CarbonImmutable::setTestNow();
});

it('shares upcoming opening days with holidays and configured closures', function () {
    CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-11-09 10:00', 'Europe/Warsaw'));
    config()->set('range.closed_dates', ['2026-11-13' => 'Zawody klubowe']);

    $this->get(route('home'))
        ->assertSuccessful()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Home/Index')
            ->where('schedule.timezone', 'Europe/Warsaw')
            ->has('schedule.days', 8)
            ->where('schedule.days.0.date', '2026-11-09')
            ->where('schedule.days.0.is_open', true)
            ->where('schedule.days.0.opens_at', '2026-11-09T09:00:00+01:00')
            ->where('schedule.days.0.closes_at', '2026-11-09T18:00:00+01:00')
            ->where('schedule.days.2.closure_reason', 'Święto Niepodległości')
            ->where('schedule.days.4.is_open', false)
            ->where('schedule.days.4.closure_reason', 'Zawody klubowe')
            ->where('location.address.street', 'Stawiskowska 57A')
            ->has('location.maps_url')
        );
});

it('shares the cheapest shot price per gun type sorted by price', function () {
    $rifleType = GunType::factory()->create(['name' => 'Karabin']);
    $pistolType = GunType::factory()->create(['name' => 'Pistolet']);
    $emptyType = GunType::factory()->create(['name' => 'Rewolwer']);

    $rimfire = Caliber::factory()->create();
    $centerfire = Caliber::factory()->create();
    Ammunition::factory()->for($rimfire)->create(['standard_price' => 1.5, 'club_price' => 1.0, 'cart_quantity_step' => 10]);
    Ammunition::factory()->for($centerfire)->create(['standard_price' => 3.0, 'club_price' => 2.0, 'cart_quantity_step' => 10]);
    Ammunition::factory()->for($centerfire)->create(['standard_price' => 4.0, 'club_price' => 3.0, 'cart_quantity_step' => 5]);

    Gun::factory()->for($rifleType)->for($rimfire)->create(['name' => 'CZ 457']);
    Gun::factory()->for($rifleType)->for($centerfire)->create(['name' => 'WBP Jack']);
    Gun::factory()->for($pistolType)->for($centerfire)->create(['name' => 'Glock 19']);
    Gun::factory()->for($emptyType)->create();

    $this->get(route('home'))
        ->assertSuccessful()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->has('priceHighlights', 2)
            ->where('priceHighlights.0.gun_type', 'Karabin')
            ->where('priceHighlights.0.guns_count', 2)
            ->where('priceHighlights.0.example_guns', ['CZ 457', 'WBP Jack'])
            ->where('priceHighlights.0.shots', 10)
            ->where('priceHighlights.0.standard_price_per_shot', 1.5)
            ->where('priceHighlights.0.club_price_per_shot', 1)
            ->where('priceHighlights.1.gun_type', 'Pistolet')
            ->where('priceHighlights.1.standard_price_per_shot', 3)
        );
});
