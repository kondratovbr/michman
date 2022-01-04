{{--TODO: IMPORTANT! This view need to accommodate logs from multiple servers.--}}
{{--TODO: IMPORTANT! I can certainly add more useful info to this view.--}}

<div class="min-h-0 flex flex-col space-y-2">

    <h3><strong>{{ $server->name }}</strong></h3>

    <x-logs :logs="$logs" />

</div>
