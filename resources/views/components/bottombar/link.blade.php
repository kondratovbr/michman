@props(['routeName'])

<a {{ $attributes->merge([
    'class' => 'w-full h-full group inline-flex items-stretch p-2 focus:outline-none cursor-pointer select-none',
]) }}
    @isset($routeName)
        href="{{ route($routeName) }}"
    @endisset
>
    <div class="flex-grow px-4 rounded-md border border-gray-200 border-opacity-0 group-focus:border-opacity-100 transition-border-background ease-in-out duration-quick flex justify-center items-center group-hover:border-opacity-100 group-hover:text-gray-100 bg-navy-400 bg-opacity-0 group-active:bg-opacity-100 text-gray-200 capitalize"
    >
        @isset($icon)
            <x-icon class="mr-2 text-2xl" size="8">{{ $icon }}</x-icon>
        @endisset
        @isset($content)
            <span class="text-sm">{{ $content }}</span>
        @endisset
        {{ $slot }}
    </div>
</a>
