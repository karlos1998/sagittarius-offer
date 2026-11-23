<?php

namespace App\Support\RangeSchedule;

use Carbon\CarbonImmutable;

class ConfiguredClosures implements SpecialDayProvider
{
    /**
     * @param  array<string, string>  $closedDates
     */
    public function __construct(private readonly array $closedDates) {}

    public static function fromConfig(): self
    {
        return new self((array) config('range.closed_dates', []));
    }

    public function reasonFor(CarbonImmutable $date): ?string
    {
        return $this->closedDates[$date->toDateString()] ?? null;
    }
}
