@props(['link' => false, 'size' => null])

@php
    $classes = implode(' ', [
        'inline-flex items-center justify-center min-w-8 rounded-md outline-none cursor-pointer select-none whitespace-nowrap',
        'py-1 px-2',
        'text-gray-300 bg-transparent ring-2 ring-gray-400 ring-opacity-0 border-2 border-gray-400 border-opacity-0',
        'hover:ring-opacity-100 hover:text-gray-200',
        'active:bg-gray-600 active:text-gray-100',
        'focus:ring-opacity-100 focus:border-opacity-100 focus:outline-none focus:ring focus:ring-opacity-50',
        'disabled:text-gray-300 disabled:bg-transparent disabled:ring-opacity-0 disabled:opacity-50 disabled:cursor-default',
        'transition-border-ring-background ease-in-out duration-quick',
    ]);
@endphp

@if($link)
    <a {{ $attributes->merge(['class' => $classes])->except('type') }}>
        <x-icon size="6"><i class="fas fa-ellipsis-h text-2xl"></i></x-icon>
    </a>
@else
    <button {{ $attributes->merge(['class' => $classes, 'type' => 'button']) }}>
        <x-icon size="6"><i class="fas fa-ellipsis-h text-2xl"></i></x-icon>
    </button>
@endif
