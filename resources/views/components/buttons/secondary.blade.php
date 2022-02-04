@props(['capitalize', 'link' => false,  'size' => null, 'icon' => null])

@php
    $classes = implode(' ', [
        'shrink-0 grow-0 inline-flex items-center justify-center min-w-16 rounded-md outline-none cursor-pointer select-none whitespace-nowrap',
        ($capitalize ?? true) ? 'capitalize' : '',
        'bg-transparent ring-2 ring-gray-400 border-2 border-gray-400 border-opacity-0',
        'mx-2px',
        match ($size ?? null) {
            'small' => 'py-0 px-2 text-sm',
            default => 'py-1 px-4',
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
        @if(! empty($icon))
            <x-icon class="-ml-1 mr-1"><i class="{{ $icon }}"></i></x-icon>
        @endif
        <span>{{ $slot }}</span>
    </a>
@else
    <button {{ $attributes->merge(['class' => $classes, 'type' => 'button']) }}>
        @if(! empty($icon))
            <x-icon class="-ml-1 mr-1"><i class="{{ $icon }}"></i></x-icon>
        @endif
        <span>{{ $slot }}</span>
    </button>
@endif
