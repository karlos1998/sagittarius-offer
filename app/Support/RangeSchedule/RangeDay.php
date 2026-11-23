<?php

namespace App\Support\RangeSchedule;

use Carbon\CarbonImmutable;

class RangeDay
{
    public function __construct(
        public readonly CarbonImmutable $date,
        public readonly ?CarbonImmutable $opensAt,
        public readonly ?CarbonImmutable $closesAt,
        public readonly ?string $closureReason = null,
        public readonly ?string $callAheadReason = null,
    ) {}

    public function isOpen(): bool
    {
        return $this->opensAt !== null && $this->closesAt !== null;
    }

    /**
     * Whether visitors should phone the range before coming (e.g. holidays with irregular hours).
     */
    public function requiresCallAhead(): bool
    {
        return $this->callAheadReason !== null;
    }

    /**
     * @return array{date: string, weekday: int, is_open: bool, opens_at: ?string, closes_at: ?string, closure_reason: ?string, call_ahead_reason: ?string}
     */
    public function toArray(): array
    {
        return [
            'date' => $this->date->toDateString(),
            'weekday' => $this->date->dayOfWeekIso,
            'is_open' => $this->isOpen(),
            'opens_at' => $this->opensAt?->toIso8601String(),
            'closes_at' => $this->closesAt?->toIso8601String(),
            'closure_reason' => $this->closureReason,
            'call_ahead_reason' => $this->callAheadReason,
        ];
    }
}
