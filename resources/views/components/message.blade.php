@props(['colors' => 'info'])

<div {{ $attributes->class([
    'py-5 px-6 rounded-lg',
    match ($colors) {
        'info' => 'bg-navy-500',
        'danger' => 'bg-red-700 text-red-100',
    },
]) }}>
    {{ $slot }}
</div>
