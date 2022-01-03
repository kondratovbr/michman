{{--TODO: IMPORTANT! This view need to accommodate logs from multiple servers.--}}
{{--TODO: CRITICAL! CONTINUE. Check this out in the UI and figure out if something more needs to be shown. It probably is. --}}

<div class="min-h-0 flex flex-col space-y-2">

    <h3><strong>{{ $server->name }}</strong></h3>

    <x-logs :logs="$logs" />

</div>
