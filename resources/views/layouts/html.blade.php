<!doctype html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    id="app"
    class="dark"
>
    <head>
        {{-- Technical Metas --}}
        <meta charset="UTF-8">
        <meta
            name="viewport"
            content="width=device-width, initial-scale=1"
{{--            This option should prevent zooming on mobile. Maybe.--}}
{{--            content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0"--}}
        >
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <meta name="referrer" content="always">

        @include('partials._favicons')

        {{-- CSRF-token for front-end scripts is provided as a meta. --}}
        <meta name="csrf-token" content="{{ csrf_token() }}">

        {{-- Fonts --}}
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=IBM+Plex+Serif:wght@400;600;700&display=swap">

        {{-- Styles --}}
        <link rel="stylesheet" href="{{ mix('css/app.css') }}">
        @livewireStyles

        {{-- Scripts --}}
        <script defer src="{{ mix('js/app.js') }}"></script>
        {{--
        TODO: IMPORTANT! Should I keep these Ace Editor sources here like that for production?
              Is there a better way? Google how to install the Ace Editor for production in general. And see how Forge does it.
              Serve locally? Package in app.js? Serve from a CDN but check it during some health check regularly?
              NOTE: These scripts cannot be just "defer"red - they should be ran before the <script> tag that declares the actual editor field.
              These should probably be put above the rest of the page - so the browser starts loading them in parallel with the rest of the page.
              https://devdojo.com/tnylea/using-ace-editor-with-livewire
        --}}
        <script
            src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.12/ace.min.js"
            integrity="sha512-GoORoNnxst42zE3rYPj4bNBm0Q6ZRXKNH2D9nEmNvVF/z24ywVnijAWVi/09iBiVDQVf3UlZHpzhAJIdd9BXqw=="
            crossorigin="anonymous"
            referrerpolicy="no-referrer"
            type="text/javascript"
            charset="utf-8"
        ></script>
        <script
            src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.12/theme-monokai.min.js"
            integrity="sha512-S4i/WUGRs22+8rjUVu4kBjfNuBNp8GVsgcK2lbaFdws4q6TF3Nd00LxqnHhuxS9iVDfNcUh0h6OxFUMP5DBD+g=="
            crossorigin="anonymous"
            referrerpolicy="no-referrer"
            type="text/javascript"
            charset="utf-8"
        ></script>
        <script
            src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.12/mode-sh.min.js"
            integrity="sha512-e1lzPcRUUhfM9oRrV0pgJs+rAJMA1OGXUYSxlX2UZwaO/GvqlL5ZUKwE2lNf5I/Wq6S6ua0U4GWaRrC2J9AXIw=="
            crossorigin="anonymous"
            referrerpolicy="no-referrer"
            type="text/javascript"
            charset="utf-8"
        ></script>
        <script
            src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.12/mode-python.min.js"
            integrity="sha512-2Ke4vMGrMfYRM55pT1aA5bw7Pl82Sc7K5Hg8XZYZu+EQrb0AO1mNYTagwZm+MFVAImYS9Mlnm73zcgc01wPXxA=="
            crossorigin="anonymous"
            referrerpolicy="no-referrer"
            type="text/javascript"
            charset="utf-8"
        ></script>

        {{-- Page-specific --}}
        {{-- TODO: Make this title page-specific. --}}
        <title>{{ config('app.name', 'App') }}</title>
        <link rel="canonical" href="{{ url()->current() }}">
        <meta name="description" content="{{ __('general.description') }}">

        @include('partials._socials')

        @production
            @include('partials._reddit-pixel')
        @endproduction

        @stack('scripts')

{{--        TODO: CRITICAL! Add SEO and misc metas. Don't forget to fill out these "canonical" and "description"! --}}
    </head>
    <body class="relative font-sans antialiased w-screen overflow-x-hidden bg-navy-100 text-gray-100 {{ isDebug() ? 'debug-screens' : null }}">

{{--        TODO: Add big and visible dev/stage marker somewhere just for convenience.--}}

        {{ $slot }}

        @include('partials._js-env')

        @livewireScripts

        @stack('livewire')

        @include('cookie-consent::index')

    </body>
</html>
