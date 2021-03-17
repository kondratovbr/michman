{{--TODO: IMPORTANT! Unfinished. Take a good look at its styling. Also, maybe refactor for different buttons. Also, optimize transitions.--}}
{{--TODO: Add a loading spinner, like Bulma does.--}}
{{--TODO: Maybe use cursor-wait option for when the loading spinner is showing.--}}
{{--TODO: See how it looks when "disabled". Maybe needs an update.--}}
{{--TODO: IMPORTANT! Unfinished. Take a good look at its styling. Also, maybe refactor for different buttons. Also, optimize transitions (Move it to specific buttons maybe - they have different styling).--}}

<button {{ $attributes->merge([
    'class' => 'inline-flex items-center px-4 py-2.5 rounded-md font-semibold text-xs uppercase tracking-widest outline-none focus:outline-none focus:ring focus:ring-gray-300 focus:ring-opacity-75 ease-in-out duration-quick cursor-pointer select-none disabled:opacity-50 disabled:cursor-default',
]) }}>
    {{ $slot }}
</button>
