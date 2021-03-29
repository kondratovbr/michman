<x-button-new {{ $attributes->merge([
    'class' => implode(' ', [
        'py-1 mt-2px',
        'bg-transparent ring-2 ring-gray-400 border-2 border-gray-400 border-opacity-0',
        'hover:bg-gray-700',
        'active:bg-gray-600',
        'focus:bg-gray-700 focus:border-opacity-100',
        'disabled:bg-transparent',
        'transition-border-background',
    ]),
    'type' => 'button',
]) }}
    :paddingY="false"
    :border="false"
>
    {{ $slot }}
</x-button-new>
