{{--TODO: IMPORTANT! Unfinished. Take a good look at its styling. Also, maybe refactor for different buttons. Also, optimize transitions.--}}
{{--TODO: Add a loading spinner, like Bulma does.--}}
{{--TODO: Maybe use cursor-wait option for when the loading spinner is showing.--}}
{{--TODO: See how it looks when "disabled". Maybe needs an update.--}}
{{--TODO: IMPORTANT! Unfinished. Take a good look at its styling. Also, maybe refactor for different buttons. Also, optimize transitions (Move it to specific buttons maybe - they have different styling).--}}

@props(['border' => true, 'paddingY' => true])

<button {{ $attributes->merge([
    'class' => implode(' ', [
        'inline-flex items-center px-4 rounded-md font-semibold text-xs uppercase tracking-widest outline-none cursor-pointer select-none',
        $paddingY ? 'py-2' : '',
        'focus:outline-none focus:ring focus:ring-opacity-50',
        'disabled:opacity-50 disabled:cursor-default',
        'ease-in-out duration-quick',
        $border ? 'border-2 border-transparent' : ''
    ]),
]) }}>
    {{ $slot }}
</button>
