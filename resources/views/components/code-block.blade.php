{{--TODO: Looks a bit meh. Any way to improve?--}}

@props(['wrap' => false])

<pre {{ $attributes->class([
    'bg-code-bg rounded-md',
    'px-3-em py-2-em',
    'text-code-text font-mono',
    $wrap
        ? 'whitespace-pre-wrap break-all'
        : 'whitespace-pre overflow-x-scroll',
]) }}><code>{{ $slot }}</code></pre>
