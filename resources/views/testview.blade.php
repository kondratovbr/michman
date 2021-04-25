<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">

        <title>Test View</title>

        <link rel="stylesheet" href="{{ mix('css/app.css') }}">
        @livewireStyles

{{--        <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>--}}
        <script src="{{ mix('js/app.js') }}" defer></script>
    </head>

    <body class="p-20">

        <x-select
            :data="$data1"
        />

        <x-select
            :data="$data2"
        />

        @livewireScripts
    </body>
</html>
