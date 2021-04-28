@props(['margins' => true])

<p {{ $attributes->class([
    'block max-w-prose text-sm leading-normal',
    'mt-1.5' => $margins,
]) }}>{{ $slot }}</p>
