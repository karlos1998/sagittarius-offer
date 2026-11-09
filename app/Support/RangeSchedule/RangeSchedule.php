<?php

namespace App\Support\RangeSchedule;

use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;

class RangeSchedule
{
    private const WEEKDAYS = [
        1 => 'monday',
        2 => 'tuesday',
        3 => 'wednesday',
        4 => 'thursday',
        5 => 'friday',
        6 => 'saturday',
        7 => 'sunday',
    ];

    /**
     * @param  array<string, array{0: string, 1: string}|null>  $weeklyHours
     * @param  iterable<ClosureProvider>  $closureProviders
     */
    public function __construct(
        private readonly array $weeklyHours,
        private readonly string $timezone,
        private readonly iterable $closureProviders = [],
    ) {}

    public static function fromConfig(): self
    {
        return new self(
            (array) config('range.weekly_hours', []),
            (string) config('range.timezone', 'Europe/Warsaw'),
            collect((array) config('range.closure_providers', []))
                ->map(fn (string $provider): ClosureProvider => method_exists($provider, 'fromConfig')
                    ? $provider::fromConfig()
                    : app($provider))
                ->all(),
        );
    }

    public function timezone(): string
    {
        return $this->timezone;
    }

    public function day(CarbonInterface $date): RangeDay
    {
        $day = CarbonImmutable::instance($date)->setTimezone($this->timezone)->startOfDay();

        foreach ($this->closureProviders as $closureProvider) {
            $reason = $closureProvider->closureReasonFor($day);

            if ($reason !== null) {
                return new RangeDay($day, null, null, $reason);
            }
        }

        $hours = $this->weeklyHours[self::WEEKDAYS[$day->dayOfWeekIso]] ?? null;

        if ($hours === null) {
            return new RangeDay($day, null, null);
        }

        return new RangeDay(
            $day,
            $this->atTime($day, $hours[0]),
            $this->atTime($day, $hours[1]),
        );
    }

    /**
     * @return array<int, RangeDay>
     */
    public function upcomingDays(CarbonInterface $from, int $count): array
    {
        $start = CarbonImmutable::instance($from)->setTimezone($this->timezone)->startOfDay();

        return collect(range(0, $count - 1))
            ->map(fn (int $offset): RangeDay => $this->day($start->addDays($offset)))
            ->all();
    }

    private function atTime(CarbonImmutable $day, string $time): CarbonImmutable
    {
        [$hour, $minute] = array_map('intval', explode(':', $time));

        return $day->setTime($hour, $minute);
    }
}
