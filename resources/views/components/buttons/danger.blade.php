<x-button-new {{ $attributes->merge([
    'class' => implode(' ', [
        'bg-red-600 text-gray-100',
        'hover:bg-red-700',
        'active:bg-red-800',
        'focus:bg-red-700 focus:ring-red-500',
        'disabled:bg-gold-600',
        'transition-ring-background',
    ]),
    'type' => 'button',
]) }}>
    {{ $slot }}
</x-button-new>
