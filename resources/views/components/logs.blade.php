@props(['logs'])

{{--TODO: IMPORTANT! Make the thing wider? So standard 80 characters would actually fit. --}}

<div class="bg-code-bg rounded-md px-3-em py-2-em text-code-text font-mono max-h-full min-h-0 overflow-scroll">
    @foreach($logs ?? [] as $log)
        @if($log->renderable)
            @if(! empty($log->command))
                <pre class="mt-8 whitespace-pre-wrap break-all"><code>$ {{ trim($log->command) }}</code></pre>
            @endif
            @if(! empty($log->content))
                <pre class="mt-3 whitespace-pre-wrap break-all"><code>{{ trim($log->content) }}</code></pre>
            @endif
        @endif
    @endforeach
</div>
