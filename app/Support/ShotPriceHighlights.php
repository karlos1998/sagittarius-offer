<?php

namespace App\Support;

use App\Models\Ammunition;
use App\Models\Gun;
use App\Models\GunType;
use Illuminate\Support\Collection;

class ShotPriceHighlights
{
    private const EXAMPLE_GUNS_LIMIT = 3;

    /**
     * Cheapest way to shoot each gun type, sorted from the cheapest one.
     *
     * @return array<int, array{gun_type: string, guns_count: int, example_guns: array<int, string>, shots: int, standard_price_per_shot: float, club_price_per_shot: float}>
     */
    public function perGunType(): array
    {
        return GunType::query()
            ->with('guns.caliber.ammunitions')
            ->get()
            ->map(fn (GunType $gunType): ?array => $this->highlightFor($gunType))
            ->filter()
            ->sortBy('standard_price_per_shot')
            ->values()
            ->all();
    }

    /**
     * @return array{gun_type: string, guns_count: int, example_guns: array<int, string>, shots: int, standard_price_per_shot: float, club_price_per_shot: float}|null
     */
    private function highlightFor(GunType $gunType): ?array
    {
        /** @var Collection<int, Gun> $guns */
        $guns = $gunType->guns->filter(fn (Gun $gun): bool => $gun->caliber?->ammunitions->isNotEmpty() ?? false);

        if ($guns->isEmpty()) {
            return null;
        }

        $ammunitions = $guns->flatMap(fn (Gun $gun): Collection => $gun->caliber->ammunitions);

        /** @var Ammunition $cheapest */
        $cheapest = $ammunitions->sortBy(fn (Ammunition $ammunition): float => (float) $ammunition->standard_price)->first();

        $exampleGuns = $guns
            ->sortBy(fn (Gun $gun): float => (float) $gun->caliber->ammunitions->min('standard_price'))
            ->take(self::EXAMPLE_GUNS_LIMIT)
            ->pluck('name')
            ->values()
            ->all();

        return [
            'gun_type' => $gunType->name,
            'guns_count' => $guns->count(),
            'example_guns' => $exampleGuns,
            'shots' => max(1, (int) $cheapest->cart_quantity_step),
            'standard_price_per_shot' => (float) $cheapest->standard_price,
            'club_price_per_shot' => (float) $ammunitions->min(fn (Ammunition $ammunition): float => (float) $ammunition->club_price),
        ];
    }
}
