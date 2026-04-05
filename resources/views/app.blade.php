<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>
        <meta name="description" content="Oficjalna oferta KS Sagittarius. Wybierz broń i pakiety strzeleckie oraz złóż zamówienie online.">
        <meta name="robots" content="index,follow">

        <meta property="og:type" content="website">
        <meta property="og:site_name" content="{{ config('app.name', 'KS Sagittarius') }}">
        <meta property="og:locale" content="pl_PL">
        <meta property="og:title" content="{{ config('app.name', 'KS Sagittarius') }}">
        <meta property="og:description" content="Oficjalna oferta KS Sagittarius. Wybierz broń i pakiety strzeleckie oraz złóż zamówienie online.">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:image" content="{{ url('/images/branding/kss-logo.png') }}">

        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ config('app.name', 'KS Sagittarius') }}">
        <meta name="twitter:description" content="Oficjalna oferta KS Sagittarius. Wybierz broń i pakiety strzeleckie oraz złóż zamówienie online.">
        <meta name="twitter:image" content="{{ url('/images/branding/kss-logo.png') }}">
        <link rel="canonical" href="{{ url()->current() }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
