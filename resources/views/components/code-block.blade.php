{{--TODO: Looks a bit meh. Any way to improve? See logs.blade.php and code.blade.php as well.--}}

@props(['wrap' => false])

<div {{ $attributes->class([
    'max-h-full overflow-y-scroll',
    'bg-code-bg rounded-md',
    'px-3-em py-2-em',
    'text-code-text font-mono',
]) }}>
    <pre
        class="{{ $wrap ? 'whitespace-pre-wrap break-all' : 'whitespace-pre overflow-x-scroll' }}"
    ><code>{{ $slot }}</code></pre>
</div>
