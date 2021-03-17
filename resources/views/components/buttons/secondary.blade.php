<x-button-new {{ $attributes->merge([
    'class' => 'py-2 ring-2 ring-inset ring-gray-400 border-0 hover:bg-gray-700 active:bg-gray-600 disabled:bg-transparent transition-background',
    'type' => 'button',
]) }}>
    {{ $slot }}
</x-button-new>
