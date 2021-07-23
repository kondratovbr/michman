@props(['margins' => true])

<div {{ $attributes->class([
    'block max-w-prose text-sm leading-normal',
    'mt-1.5' => $margins,
]) }}>{{ $slot }}</div>
