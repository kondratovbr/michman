@props(['capitalize', 'link' => false,  'size' => null])

@php
    $classes = implode(' ', [
        'inline-flex items-center justify-center min-w-16 rounded-md outline-none cursor-pointer select-none whitespace-nowrap',
        ($capitalize ?? true) ? 'capitalize' : '',
        'bg-transparent ring-2 ring-gray-400 border-2 border-gray-400 border-opacity-0',
        match ($size ?? null) {
            'small' => 'py-0 px-2 mt-2px text-sm',
            default => 'py-1 px-4 mt-2px',
        },
        'hover:bg-gray-700',
        'active:bg-gray-600',
        'focus:bg-gray-700 focus:border-opacity-100 focus:outline-none focus:ring focus:ring-opacity-50',
        'disabled:bg-transparent disabled:opacity-50 disabled:cursor-default',
        'transition-border-background ease-in-out duration-quick',
    ]);
@endphp

@if($link)
    <a {{ $attributes->merge(['class' => $classes])->except('type') }}>
        {{ $slot }}
    </a>
@else
    <button {{ $attributes->merge(['class' => $classes, 'type' => 'button']) }}>
        {{ $slot }}
    </button>
@endif
