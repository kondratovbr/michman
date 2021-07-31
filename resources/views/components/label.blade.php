@props(['value'])

<label {{ $attributes->class([
    'block mb-1 group-disabled:opacity-50',
]) }}>
    {{ $value ?? $slot }}
</label>
