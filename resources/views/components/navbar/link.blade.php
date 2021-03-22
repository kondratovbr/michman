<a
    {{ $attributes->merge([
        'class' => 'group inline-flex items-stretch px-2 py-2 focus:outline-none',
        'href' => route($routeName),
    ]) }}
>
    <div class="px-4 rounded-md border border-gray-200 border-opacity-0 {{ $stateClasses }} group-focus:border-opacity-100 transition-border-background ease-in-out duration-quick flex items-center">
        <div class="text-gray-200 transform {{ $contentStateClasses }} group-focus:scale-110 transition-transform ease-in-out duration-quick">
            @isset($icon)
                <x-icon class="mr-1.5">{{ $icon }}</x-icon>
            @endisset
            <span>{{ $slot }}</span>
        </div>
    </div>
</a>
