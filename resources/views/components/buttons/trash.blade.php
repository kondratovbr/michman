@props(['capitalize', 'link' => false, 'size' => null])

@php
    $classes = implode(' ', [
        'inline-flex items-center justify-center rounded-md outline-none cursor-pointer select-none whitespace-nowrap',
        ($capitalize ?? true) ? 'capitalize' : '',
        match ($size ?? null) {
            'small' => 'py-0.5 px-2',
            default => 'py-1.5 px-4'
        },
        'bg-red-600 text-gray-100',
        'hover:bg-red-700',
        'active:bg-red-800',
        'focus:bg-red-700 focus:ring-red-500 focus:outline-none focus:ring focus:ring-opacity-50',
        'disabled:bg-red-600 disabled:opacity-50 disabled:cursor-default',
        'transition-ring-background ease-in-out duration-quick',
        'border-2 border-transparent',
    ]);
@endphp

@if($link)
    <a {{ $attributes->merge(['class' => $classes])->except('type') }}>
        <x-icon><i class="far fa-trash-alt"></i></x-icon>
    </a>
@else
    <button {{ $attributes->merge(['class' => $classes, 'type' => 'button']) }}>
        <x-icon><i class="far fa-trash-alt"></i></x-icon>
    </button>
@endif
