@props(['value'])

<label {{ $attributes->class([
    'block mb-1',
]) }}>
    {{ $value ?? $slot }}
</label>
