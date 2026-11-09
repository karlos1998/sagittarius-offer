<?php

use App\Support\RangeSchedule\ConfiguredClosures;
use App\Support\RangeSchedule\PolishPublicHolidays;

return [

    /*
    |--------------------------------------------------------------------------
    | Range timezone
    |--------------------------------------------------------------------------
    */

    'timezone' => 'Europe/Warsaw',

    /*
    |--------------------------------------------------------------------------
    | Weekly opening hours
    |--------------------------------------------------------------------------
    |
    | Opening and closing time for each weekday ("HH:MM"). Set a day to null
    | to keep the range closed on that weekday.
    |
    */

    'weekly_hours' => [
        'monday' => ['09:00', '18:00'],
        'tuesday' => ['09:00', '18:00'],
        'wednesday' => ['09:00', '18:00'],
        'thursday' => ['09:00', '18:00'],
        'friday' => ['09:00', '18:00'],
        'saturday' => ['09:00', '18:00'],
        'sunday' => ['09:00', '18:00'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Closure providers
    |--------------------------------------------------------------------------
    |
    | Classes implementing ClosureProvider. Each one can close the range on a
    | given date and give the reason shown to visitors. Add a provider here
    | (e.g. competitions stored in the database) to close more days.
    |
    */

    'closure_providers' => [
        PolishPublicHolidays::class,
        ConfiguredClosures::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | One-off closed days
    |--------------------------------------------------------------------------
    |
    | Dates ("YYYY-MM-DD") on which the range is closed, with the reason.
    |
    */

    'closed_dates' => [
        // '2026-12-31' => 'Sylwester',
    ],

    /*
    |--------------------------------------------------------------------------
    | Location
    |--------------------------------------------------------------------------
    */

    'address' => [
        'street' => 'Stawiskowska 57A',
        'postal_code' => '18-421',
        'city' => 'Piątnica Poduchowna',
    ],

    'maps_url' => 'https://www.google.com/maps/dir/?api=1&destination=53.2025552,22.0983882&destination_place_id=ChIJjbmg2kgyHkcRFRlfZIjdDHY',

    'place_url' => 'https://www.google.com/maps/place/Klub+Strzelecki+SAGITTARIUS/@53.2025552,22.0983882,17z',

    'permit_help_url' => 'https://pomoc.kssagittarius.pl/komenda',

];
