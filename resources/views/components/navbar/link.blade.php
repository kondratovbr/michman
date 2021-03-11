{{--TODO: Make the background change on hover better - rounded edges and wider, like proper buttons - give it some space. Maybe remove bottom border on hover, leave it only for the current page. Should probably be done in the corresponding component class.--}}

<a
    {{ $attributes->merge([
        'class' => 'inline-flex items-center px-4 pt-1 border-b-2 text-gray-200 text-sm font-medium leading-5 transition ease-in-out duration-100' . ' '
        . $stateClasses,
        'href' => route($routeName),
    ]) }}
>
    {{ $slot }}
</a>
