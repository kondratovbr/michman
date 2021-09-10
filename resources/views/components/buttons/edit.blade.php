@props(['loading' => false, 'size' => null])

@php
    $classes = implode(' ', [
        'inline-flex items-center justify-center rounded-md outline-none cursor-pointer select-none whitespace-nowrap',
        match ($size ?? null) {
            'small' => 'py-0.5 px-2 text-sm',
            default => 'py-2 px-4',
        },
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
        <x-icon><i class="fas fa-pencil-alt"></i></x-icon>
    </div>
</button>
