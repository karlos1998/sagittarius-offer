<?php

namespace App\Http\Controllers;

use App\Models\Instructor;
use App\Support\MediaUrlResolver;
use App\Support\RangeSchedule\RangeDay;
use App\Support\RangeSchedule\RangeSchedule;
use App\Support\ShotPriceHighlights;
use Carbon\CarbonImmutable;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    private const SCHEDULE_DAYS_AHEAD = 8;

    public function index(RangeSchedule $rangeSchedule, ShotPriceHighlights $shotPriceHighlights): Response
    {
        $mediaUrlResolver = MediaUrlResolver::make();

        return Inertia::render('Home/Index', [
            'cart' => session()->get('cart', []),
            'instructors' => Instructor::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('full_name')
                ->get()
                ->map(fn (Instructor $instructor): array => [
                    'id' => $instructor->id,
                    'full_name' => $instructor->full_name,
                    'description' => $instructor->description,
                    'photo_url' => $mediaUrlResolver->url($instructor->photo),
                ])
                ->all(),
            'schedule' => [
                'timezone' => $rangeSchedule->timezone(),
                'generated_at' => CarbonImmutable::now()->toIso8601String(),
                'days' => array_map(
                    fn (RangeDay $day): array => $day->toArray(),
                    $rangeSchedule->upcomingDays(CarbonImmutable::now(), self::SCHEDULE_DAYS_AHEAD),
                ),
            ],
            'location' => [
                'address' => config('range.address'),
                'maps_url' => config('range.maps_url'),
                'place_url' => config('range.place_url'),
                'permit_help_url' => config('range.permit_help_url'),
            ],
            'priceHighlights' => $shotPriceHighlights->perGunType(),
        ]);
    }
}
