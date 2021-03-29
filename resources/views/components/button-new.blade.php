{{--TODO: Maybe extract buttons as a component.--}}
{{--TODO: Add a loading spinner, like Bulma does.--}}
{{--TODO: Maybe use cursor-wait option for when the loading spinner is showing.--}}
{{--TODO: See how it looks when "disabled". Maybe needs an update.--}}

@props(['border' => true, 'paddingY' => true, 'textClasses'])

<button {{ $attributes->merge([
    'class' => implode(' ', [
        'inline-flex items-center justify-center min-w-16 px-4 rounded-md outline-none cursor-pointer select-none whitespace-nowrap',
        $paddingY ? 'py-1.5' : '',
        'capitalize',
        $textClasses ?? '',
        'focus:outline-none focus:ring focus:ring-opacity-50',
        'disabled:opacity-50 disabled:cursor-default',
        'ease-in-out duration-quick',
        $border ? 'border-2 border-transparent' : ''
    ]),
]) }}>
    {{ $slot }}
</button>

{{--font-semibold text-xs uppercase tracking-widest--}}
