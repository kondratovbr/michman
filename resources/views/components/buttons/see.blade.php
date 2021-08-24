@props(['loading' => false])

@php
    $classes = implode(' ', [
        'inline-flex items-center justify-center rounded-md outline-none cursor-pointer select-none whitespace-nowrap',
        'py-1 px-3.5',
        'bg-transparent ring-2 ring-gray-400 border-2 border-gray-400 border-opacity-0',
        'hover:bg-gray-700',
        'active:bg-gray-600',
        'focus:bg-gray-700 focus:border-opacity-100 focus:outline-none focus:ring focus:ring-opacity-50',
        'disabled:bg-transparent disabled:opacity-50 disabled:cursor-default',
        'transition-border-background ease-in-out duration-quick',
    ]);
@endphp

<button {{ $attributes->merge(['class' => $classes, 'type' => 'button']) }}>
    <div class="flex justify-center items-center">
        <x-icon><i class="fas fa-eye"></i></x-icon>
    </div>
</button>