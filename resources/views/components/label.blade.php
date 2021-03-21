@props(['value'])

<label {{ $attributes->merge([
    'class' => 'block mb-1'
]) }}>
    {{ $value ?? $slot }}
</label>
