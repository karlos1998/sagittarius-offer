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
        'monday' => ['10:00', '18:00'],
        'tuesday' => ['10:00', '18:00'],
        'wednesday' => ['10:00', '18:00'],
        'thursday' => ['10:00', '18:00'],
        'friday' => ['10:00', '18:00'],
        'saturday' => ['10:00', '18:00'],
        'sunday' => ['10:00', '18:00'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Closure providers
    |--------------------------------------------------------------------------
    |
    | Classes implementing SpecialDayProvider. Each one can close the range on
    | a given date and give the reason shown to visitors. Add a provider here
    | (e.g. competitions stored in the database) to close more days.
    |
    */

    'closure_providers' => [
        ConfiguredClosures::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Call-ahead providers
    |--------------------------------------------------------------------------
    |
    | Classes implementing SpecialDayProvider for days with no fixed hours.
    | The range is often open on holidays, but with different hours, so
    | visitors are asked to phone the range contact before coming.
    |
    */

    'call_ahead_providers' => [
        PolishPublicHolidays::class,
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

    /*
    |--------------------------------------------------------------------------
    | Range contact
    |--------------------------------------------------------------------------
    |
    | Person to call on holidays and other days without fixed hours.
    |
    */

    'contact' => [
        'name' => 'Arkadiusz Lesiński',
        'phone' => '+48668221032',
        'phone_display' => '668 221 032',
    ],

    'permit_help_url' => 'https://pomoc.kssagittarius.pl/komenda',

];
