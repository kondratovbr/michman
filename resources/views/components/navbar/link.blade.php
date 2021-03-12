{{--TODO: Make the background change on hover better - rounded edges and wider, like proper buttons - give it some space. Maybe remove bottom border on hover, leave it only for the current page. Should probably be done in the corresponding component class.--}}

<a
    {{ $attributes->merge([
        'class' => 'group inline-flex items-stretch px-2 py-2 text-gray-200 focus:outline-none',
        'href' => route($routeName),
    ]) }}
>
    <div class="px-4 rounded-md text-sm font-medium leading-5 border border-gray-200 border-opacity-0 {{ $stateClasses }} group-focus:border-opacity-100 transition-border-background ease-in-out duration-100 flex items-center">
        {{ $slot }}
    </div>
</a>
