@props(['logs'])

<div class="bg-code-bg rounded-md px-3-em py-2-em text-code-text font-mono">
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
