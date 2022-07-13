<!doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <meta name="referrer" content="always">

        <meta name="csrf-token" content="{{ csrf_token() }}">

        <link rel="stylesheet" href="{{ mix('css/app.css') }}">
        @livewireStyles

        <script src="{{ mix('js/app.js') }}" defer></script>

        <title>Blank Page</title>
    </head>
    <body>

        <p>url(request()->path()): {{ url(request()->path()) }}</p>

        @livewireScripts

    </body>
</html>
