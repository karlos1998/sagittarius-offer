<?php

use App\Support\RangeSchedule\ConfiguredClosures;
use App\Support\RangeSchedule\PolishPublicHolidays;
use App\Support\RangeSchedule\RangeSchedule;
use Carbon\CarbonImmutable;

function rangeSchedule(array $closedDates = []): RangeSchedule
{
    return new RangeSchedule(
        weeklyHours: [
            'monday' => ['09:00', '18:00'],
            'tuesday' => ['09:00', '18:00'],
            'wednesday' => ['09:00', '18:00'],
            'thursday' => ['09:00', '18:00'],
            'friday' => ['09:00', '18:00'],
            'saturday' => ['10:00', '16:30'],
            'sunday' => null,
        ],
        timezone: 'Europe/Warsaw',
        closureProviders: [new PolishPublicHolidays, new ConfiguredClosures($closedDates)],
    );
}

it('opens and closes at the configured weekday hours in the range timezone', function () {
    $day = rangeSchedule()->day(CarbonImmutable::parse('2026-11-14 12:00', 'Europe/Warsaw'));

    expect($day->isOpen())->toBeTrue()
        ->and($day->opensAt->toIso8601String())->toBe('2026-11-14T10:00:00+01:00')
        ->and($day->closesAt->toIso8601String())->toBe('2026-11-14T16:30:00+01:00')
        ->and($day->closureReason)->toBeNull();
});

it('keeps the range closed on weekdays without hours', function () {
    $day = rangeSchedule()->day(CarbonImmutable::parse('2026-11-15', 'Europe/Warsaw'));

    expect($day->isOpen())->toBeFalse()
        ->and($day->closureReason)->toBeNull();
});

it('closes on public holidays with the holiday name as reason', function () {
    $day = rangeSchedule()->day(CarbonImmutable::parse('2026-11-11', 'Europe/Warsaw'));

    expect($day->isOpen())->toBeFalse()
        ->and($day->closureReason)->toBe('Święto Niepodległości');
});

it('closes on configured one-off dates', function () {
    $day = rangeSchedule(['2026-11-20' => 'Zawody klubowe'])
        ->day(CarbonImmutable::parse('2026-11-20', 'Europe/Warsaw'));

    expect($day->isOpen())->toBeFalse()
        ->and($day->closureReason)->toBe('Zawody klubowe');
});

it('uses the range local date even when given a utc time', function () {
    $day = rangeSchedule()->day(CarbonImmutable::parse('2026-11-12 23:30', 'UTC'));

    expect($day->date->toDateString())->toBe('2026-11-13');
});

it('lists consecutive upcoming days starting today', function () {
    $days = rangeSchedule()->upcomingDays(CarbonImmutable::parse('2026-11-09 20:00', 'Europe/Warsaw'), 7);

    expect($days)->toHaveCount(7)
        ->and(array_map(fn ($day) => $day->date->toDateString(), $days))->toBe([
            '2026-11-09', '2026-11-10', '2026-11-11', '2026-11-12',
            '2026-11-13', '2026-11-14', '2026-11-15',
        ])
        ->and($days[2]->toArray())->toMatchArray([
            'is_open' => false,
            'closure_reason' => 'Święto Niepodległości',
            'weekday' => 3,
        ]);
});
