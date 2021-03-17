<x-button-new {{ $attributes->merge([
    'class' => 'bg-gold-800 text-gray-900 hover:bg-gold-700 active:bg-gold-600 disabled:bg-gold-800 transition-background',
    'type' => 'submit',
]) }}>
    {{ $slot }}
</x-button-new>
