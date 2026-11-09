<?php

namespace App\Support\RangeSchedule;

use Carbon\CarbonImmutable;

interface ClosureProvider
{
    /**
     * Reason the range is closed on the given date, or null when it is not.
     */
    public function closureReasonFor(CarbonImmutable $date): ?string;
}
