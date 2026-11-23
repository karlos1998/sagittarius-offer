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
     * @param  iterable<SpecialDayProvider>  $closureProviders
     * @param  iterable<SpecialDayProvider>  $callAheadProviders
     */
    public function __construct(
        private readonly array $weeklyHours,
        private readonly string $timezone,
        private readonly iterable $closureProviders = [],
        private readonly iterable $callAheadProviders = [],
    ) {}

    public static function fromConfig(): self
    {
        return new self(
            (array) config('range.weekly_hours', []),
            (string) config('range.timezone', 'Europe/Warsaw'),
            self::providersFromConfig('range.closure_providers'),
            self::providersFromConfig('range.call_ahead_providers'),
        );
    }

    public function timezone(): string
    {
        return $this->timezone;
    }

    public function day(CarbonInterface $date): RangeDay
    {
        $day = CarbonImmutable::instance($date)->setTimezone($this->timezone)->startOfDay();

        $closureReason = $this->firstReason($this->closureProviders, $day);

        if ($closureReason !== null) {
            return new RangeDay($day, null, null, closureReason: $closureReason);
        }

        $callAheadReason = $this->firstReason($this->callAheadProviders, $day);

        if ($callAheadReason !== null) {
            return new RangeDay($day, null, null, callAheadReason: $callAheadReason);
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

    /**
     * @return array<int, SpecialDayProvider>
     */
    private static function providersFromConfig(string $key): array
    {
        return collect((array) config($key, []))
            ->map(fn (string $provider): SpecialDayProvider => method_exists($provider, 'fromConfig')
                ? $provider::fromConfig()
                : app($provider))
            ->all();
    }

    /**
     * @param  iterable<SpecialDayProvider>  $providers
     */
    private function firstReason(iterable $providers, CarbonImmutable $day): ?string
    {
        foreach ($providers as $provider) {
            $reason = $provider->reasonFor($day);

            if ($reason !== null) {
                return $reason;
            }
        }

        return null;
    }

    private function atTime(CarbonImmutable $day, string $time): CarbonImmutable
    {
        [$hour, $minute] = array_map('intval', explode(':', $time));

        return $day->setTime($hour, $minute);
    }
}
