@props(['loading' => false, 'size' => null])

@php
    $classes = implode(' ', [
        'inline-flex items-center justify-center rounded-md outline-none cursor-pointer select-none whitespace-nowrap',
        match ($size ?? null) {
            'small' => 'py-0.5 px-2',
            default => 'py-2 px-4',
        },
        'bg-gray-700 bg-opacity-0 ring-2 ring-gray-800 ring-opacity-0 border-2 border-gray-400 border-opacity-0',
        'hover:ring-opacity-50',
        'active:bg-opacity-50',
        'focus:bg-opacity-75 focus:border-opacity-100 focus:outline-none focus:ring focus:ring-opacity-25',
        'disabled:bg-transparent disabled:opacity-50 disabled:cursor-default',
        'transition-border-ring-background ease-in-out duration-quick',
    ]);
@endphp

<button {{ $attributes->merge([
    'class' => $classes,
    'type' => 'button',
    'aria-label' => __('misc.dismiss'),
]) }}>
    <div class="flex justify-center items-center">
        <x-icon><i class="fas fa-times"></i></x-icon>
    </div>
</button>
