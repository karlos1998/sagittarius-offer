<?php

use App\Support\RangeSchedule\PolishPublicHolidays;
use Carbon\CarbonImmutable;

it('lists every statutory holiday for a year including movable feasts', function () {
    $holidays = (new PolishPublicHolidays)->forYear(2026);

    expect($holidays)->toHaveCount(14)
        ->and($holidays['2026-04-05'])->toBe('Wielkanoc')
        ->and($holidays['2026-04-06'])->toBe('Poniedziałek Wielkanocny')
        ->and($holidays['2026-05-24'])->toBe('Zielone Świątki')
        ->and($holidays['2026-06-04'])->toBe('Boże Ciało')
        ->and($holidays['2026-11-11'])->toBe('Święto Niepodległości')
        ->and($holidays['2026-12-24'])->toBe('Wigilia');
});

it('calculates easter sunday correctly', function (int $year, string $expectedEaster) {
    $holidays = (new PolishPublicHolidays)->forYear($year);

    expect($holidays[$expectedEaster])->toBe('Wielkanoc');
})->with([
    [2024, '2024-03-31'],
    [2025, '2025-04-20'],
    [2027, '2027-03-28'],
    [2030, '2030-04-21'],
]);

it('does not treat christmas eve as a holiday before 2025', function () {
    expect((new PolishPublicHolidays)->forYear(2024))->not->toHaveKey('2024-12-24');
});

it('returns the closure reason only on holidays', function () {
    $holidays = new PolishPublicHolidays;

    expect($holidays->closureReasonFor(CarbonImmutable::parse('2026-12-25')))->toBe('Boże Narodzenie')
        ->and($holidays->closureReasonFor(CarbonImmutable::parse('2026-11-10')))->toBeNull();
});
