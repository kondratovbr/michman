{{--TODO: Make the background change on hover better - rounded edges and wider, like proper buttons - give it some space. Maybe remove bottom border on hover, leave it only for the current page. Should probably be done in the corresponding component class.--}}

<a
    {{ $attributes->merge([
        'class' => 'group inline-flex items-stretch px-2 py-2 focus:outline-none',
        'href' => route($routeName),
    ]) }}
>
    <div class="px-4 rounded-md border border-gray-200 border-opacity-0 {{ $stateClasses }} group-focus:border-opacity-100 transition-border-background ease-in-out duration-quick flex items-center">
        <div class="text-sm font-medium leading-5 text-gray-200 transform {{ $contentStateClasses }} group-focus:scale-110 transition-transform ease-in-out duration-quick">
            @isset($icon)
                <x-icon class="mr-1.5">{{ $icon }}</x-icon>
            @endisset
            <span>{{ $slot }}</span>
        </div>
    </div>
</a>
