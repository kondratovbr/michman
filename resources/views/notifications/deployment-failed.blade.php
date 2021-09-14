{{--TODO: IMPORTANT! This view need to accommodate logs from multiple servers.--}}
{{--TODO: CRITICAL! This is just a placeholder. This view obviously needs more information than just logs here. See what Forge shows for a failed deployment alert.--}}

<div class="space-y-2">

    <h3><strong>{{ $server->name }}</strong></h3>

    <x-logs :logs="$logs" />

</div>
