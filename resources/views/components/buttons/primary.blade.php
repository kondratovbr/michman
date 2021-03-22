<x-button-new {{ $attributes->merge([
    'class' => implode(' ', [
        'bg-gold-800 text-gray-900',
        'hover:bg-gold-700',
        'active:bg-gold-600',
        'focus:bg-gold-700 focus:ring-gold-800',
        'disabled:bg-gold-800',
        'transition-ring-background',
    ]),
    'type' => 'submit',
]) }}>
    {{ $slot }}
</x-button-new>
