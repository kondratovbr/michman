{{--TODO: Improve this placeholder. Show progress, show steps, add a button to stop the process and delete the server. Don't forget the server information header.--}}

<div class="flex flex-col items-center">
    <span class="text-2xl">
        {{ match ($server->state::$name) {
            'deleting' => __('servers.placeholder.deleting'),
            default => __('servers.placeholder.preparing'),
        } }}
    </span>
    <x-spinner class="mt-4 text-2xl"/>
</div>
