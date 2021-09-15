{{--TODO: Looks a bit meh. Any way to improve?--}}

@props(['size' => null])

<code {{ $attributes->class([
    'bg-code-bg rounded-md',
    'text-code-text font-mono',
    'whitespace-pre',
    match ($size) {
        'small' => 'text-sm px-1-em py-1-em',
        default => 'px-1.5-em py-1-em',
    },
]) }}>{{ $slot }}</code>
