<!doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <meta name="referrer" content="always">

        @livewireStyles

        <title>Blank Page</title>
    </head>
    <body>

        <p>Last Log ID: {{ $lastLogId }}</p>

        <pre>
            @foreach($logs as $log)
                @isset($log->command)
                    <p>{!! $log->command !!}</p>
                @endisset
                @isset($log->content)
                    <p>{!! $log->content !!}</p>
                @endisset
                @isset($log->localFile)
                    <p>{!! $log->localFile . ' -> ' . $log->remoteFile !!}</p>
                @endisset
            @endforeach
        </pre>

        @livewireScripts
    </body>
</html>
