@props(['method'])

<x-inputs.hidden
    {{ $attributes }}
    name="_method"
    value="{{ $method ?? $slot }}"
/>
