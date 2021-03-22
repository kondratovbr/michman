{{--TODO: Figure out highlighting a currently open page.--}}

<a {{ $attributes->merge([
    'class' => 'group w-full h-full flex flex-col items-stretch py-1 px-2 cursor-pointer select-none focus:outline-none',
]) }}>
    <div
        class="py-3 px-4 text-gray-200 text-sm leading-5 rounded-md border border-gray-300 border-opacity-0 bg-navy-400 bg-opacity-0 group-hover:border-opacity-100 group-active:bg-opacity-100 group-hover:text-gray-100 group-focus:border-opacity-100 transition-border-background ease-in-out duration-quick"
    >
        <div class="transform group-hover:scale-105 transition-transform ease-in-out duration-quick">
            @isset($icon)
                <x-icon class="mr-2">{{ $icon }}</x-icon>
            @endisset
            {{ $slot }}
        </div>
    </div>
</a>
