{{--TODO: Looks a bit meh. Any way to improve?--}}

<code {{ $attributes->class([
    'bg-code-bg rounded-md',
    'px-1.5-em py-1-em',
    'text-code-text font-mono',
    'whitespace-pre',
]) }}>{{ $slot }}</code>
