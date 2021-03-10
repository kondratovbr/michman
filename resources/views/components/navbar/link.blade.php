<a
    {{ $attributes->merge([
        'class' => 'inline-flex items-center px-1 pt-1 border-b-2 text-gray-200 text-sm font-medium leading-5 transition ease-in-out duration-150' . ' '
        . $stateClasses,
        'href' => route($routeName),
    ]) }}
>
    {{ $slot }}
</a>
