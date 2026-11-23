<?php

namespace App\Support\RangeSchedule;

use Carbon\CarbonImmutable;

interface SpecialDayProvider
{
    /**
     * Name of the special day (closure reason or holiday) on the given date, or null when it is a regular day.
     */
    public function reasonFor(CarbonImmutable $date): ?string;
}
