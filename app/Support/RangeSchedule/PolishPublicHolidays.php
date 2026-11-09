<?php

namespace App\Support\RangeSchedule;

use Carbon\CarbonImmutable;

class PolishPublicHolidays implements ClosureProvider
{
    /**
     * @var array<int, array<string, string>>
     */
    private array $holidaysByYear = [];

    public function closureReasonFor(CarbonImmutable $date): ?string
    {
        return $this->forYear($date->year)[$date->toDateString()] ?? null;
    }

    /**
     * Statutory public holidays (dni ustawowo wolne od pracy) for the year.
     *
     * @return array<string, string> Holiday names keyed by "YYYY-MM-DD".
     */
    public function forYear(int $year): array
    {
        if (isset($this->holidaysByYear[$year])) {
            return $this->holidaysByYear[$year];
        }

        $easter = $this->easterSunday($year);

        $holidays = [
            "{$year}-01-01" => 'Nowy Rok',
            "{$year}-01-06" => 'Święto Trzech Króli',
            $easter->toDateString() => 'Wielkanoc',
            $easter->addDay()->toDateString() => 'Poniedziałek Wielkanocny',
            "{$year}-05-01" => 'Święto Pracy',
            "{$year}-05-03" => 'Święto Konstytucji 3 Maja',
            $easter->addDays(49)->toDateString() => 'Zielone Świątki',
            $easter->addDays(60)->toDateString() => 'Boże Ciało',
            "{$year}-08-15" => 'Wniebowzięcie NMP',
            "{$year}-11-01" => 'Wszystkich Świętych',
            "{$year}-11-11" => 'Święto Niepodległości',
            "{$year}-12-25" => 'Boże Narodzenie',
            "{$year}-12-26" => 'Drugi dzień Bożego Narodzenia',
        ];

        if ($year >= 2025) {
            $holidays["{$year}-12-24"] = 'Wigilia';
        }

        ksort($holidays);

        return $this->holidaysByYear[$year] = $holidays;
    }

    /**
     * Gregorian Easter Sunday (anonymous Gregorian algorithm), without ext-calendar.
     */
    private function easterSunday(int $year): CarbonImmutable
    {
        $a = $year % 19;
        $b = intdiv($year, 100);
        $c = $year % 100;
        $d = intdiv($b, 4);
        $e = $b % 4;
        $f = intdiv($b + 8, 25);
        $g = intdiv($b - $f + 1, 3);
        $h = (19 * $a + $b - $d - $g + 15) % 30;
        $i = intdiv($c, 4);
        $k = $c % 4;
        $l = (32 + 2 * $e + 2 * $i - $h - $k) % 7;
        $m = intdiv($a + 11 * $h + 22 * $l, 451);
        $month = intdiv($h + $l - 7 * $m + 114, 31);
        $day = (($h + $l - 7 * $m + 114) % 31) + 1;

        return CarbonImmutable::create($year, $month, $day);
    }
}
