<x-button-new {{ $attributes->merge([
    'class' => implode(' ', [
        'py-1 mt-2px',
        'text-gray-300 bg-transparent ring-2 ring-gray-400 ring-opacity-0 border-2 border-gray-400 border-opacity-0',
        'hover:ring-opacity-100 hover:text-gray-200',
        'active:bg-gray-600 active:text-gray-100',
        'focus:ring-opacity-100 focus:border-opacity-100',
        'disabled:ring-opacity-0',
        'transition-border-ring-background',
    ]),
    'type' => 'button',
]) }}
    :paddingY="false"
    :border="false"
    textClasses="underline text-sm"
>
    {{ $slot }}
</x-button-new>
