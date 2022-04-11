<a {{ $attributes->class([
    'relative group inline-flex items-stretch p-2 focus:outline-none select-none',
    'opacity-50 cursor-default' => $disabled,
]) }}
    @unless($disabled)
        href="{{ isset($routeName) ? route($routeName) : $href }}"
    @endunless
>
    <div
        class="px-4 rounded-md border border-gray-200 border-opacity-0 {{ $disabled ? '' : $stateClasses }} group-focus:border-opacity-100 transition-border-background ease-in-out duration-quick flex items-center"
    >
        <div class="text-gray-200 capitalize transform {{ $disabled ? '' : $contentStateClasses }} group-focus:scale-110 transition-transform ease-in-out duration-quick">
            @isset($icon)
                <x-icon class="mr-1.5">{{ $icon }}</x-icon>
            @endisset
            <span>{{ $slot }}</span>
        </div>
    </div>
</a>
