{{--TODO: Maybe extract buttons as a component.--}}
{{--TODO: Add a loading spinner, like Bulma does.--}}
{{--TODO: Maybe use cursor-wait option for when the loading spinner is showing.--}}
{{--TODO: See how it looks when "disabled". Maybe needs an update.--}}

@props(['textClasses', 'link' => false])

@php
    $classes = implode(' ', [
        'inline-flex items-center justify-center min-w-16 px-4 rounded-md outline-none cursor-pointer select-none whitespace-nowrap',
        ($paddingY ?? true) ? 'py-1.5' : '',
        ($capitalize ?? true) ? 'capitalize' : '',
        $textClasses ?? '',
        'focus:outline-none focus:ring focus:ring-opacity-50',
        'disabled:opacity-50 disabled:cursor-default',
        'ease-in-out duration-quick',
        ($border ?? true) ? 'border-2 border-transparent' : ''
    ]);
@endphp

@if($link)
    <a {{ $attributes->merge(['class' => $classes])->except('type') }}>
        {{ $slot }}
    </a>
@else
    <button {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif
