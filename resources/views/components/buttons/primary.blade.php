{{--TODO: IMPORTANT! Check out other buttons for these TODOs as well, they're now completely separate components:--}}
{{--TODO: Maybe extract buttons as a component.--}}
{{--TODO: Maybe use cursor-wait option for when the loading spinner is showing.--}}
{{--TODO: See how it looks when "disabled". Maybe needs an update. Check other buttons as well.--}}

@props(['capitalize', 'link' => false, 'size' => null, 'loading' => false])

@php
    $classes = implode(' ', [
        'shrink-0 grow-0 inline-flex items-center justify-center min-w-16 rounded-md outline-none cursor-pointer select-none whitespace-nowrap',
        ($capitalize ?? true) ? 'capitalize' : '',
        match ($size ?? null) {
            'small' => 'py-0.5 px-2 text-sm',
            default => 'py-1.5 px-4'
        },
        'bg-gold-800 text-gray-900 border-2 border-transparent',
        'hover:bg-gold-700',
        'active:bg-gold-600',
        'focus:bg-gold-700 focus:ring-gold-800 focus:outline-none focus:ring focus:ring-opacity-50',
        'disabled:bg-gold-800 disabled:opacity-50 disabled:cursor-default',
        'transition-ring-background ease-in-out duration-quick',
    ]);
@endphp

@if($link)
    <a {{ $attributes->merge([
        'class' => $classes,
        'disabled' => $loading,
    ])->except('type') }}>
        @if($loading)
            <x-spinner class="-ml-1 mr-2"/>
        @endif
        {{ $slot }}
    </a>
@else
    <button {{ $attributes->merge([
        'class' => $classes,
        'type' => 'submit',
        'disabled' => $loading,
    ]) }}>
        @if($loading)
            <x-spinner class="-ml-1 mr-2"/>
        @endif
        {{ $slot }}
    </button>
@endif
