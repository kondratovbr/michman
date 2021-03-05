<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        {{-- Technical Metas --}}
        <meta charset="UTF-8">
        <meta
            name="viewport"
            content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0"
        >
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <meta name="referrer" content="always">

        {{-- CSRF-token for front-end scripts is provided as a meta. --}}
        <meta name="csrf-token" content="{{ csrf_token() }}">

        {{-- Fonts --}}
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

        {{-- Styles --}}
        <link rel="stylesheet" href="{{ mix('css/app.css') }}">
        @livewireStyles

        {{-- Scripts --}}
        <script src="{{ mix('js/app.js') }}" defer></script>

        {{-- Page-specific --}}
        <title>{{ config('app.name', 'Laravel') }}</title>
        <link rel="canonical" href="{{ url()->current() }}">
        <meta name="description" content="">
{{--        TODO: IMPORTANT! Add SEO and misc metas. Don't forget to fill out these "canonical" and "description"! --}}
    </head>
    <body class="font-sans antialiased">
        {{ $slot }}

        @livewireScripts
    </body>
</html>
