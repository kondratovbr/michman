{{--TODO: Figure out highlighting a currently open page.--}}

@props(['capitalize' => true, 'textClasses' => ''])

<a {{ $attributes->merge([
    'class' => 'group h-full flex flex-col items-stretch px-2 cursor-pointer select-none focus:outline-none border-b border-gray-600 md:border-b-0 last:border-b-0 mx-4 md:mx-0 py-2 md:py-1',
]) }}>
    <div
        class="py-3 px-4 rounded-md border border-gray-300 border-opacity-0 bg-navy-400 bg-opacity-0 group-hover:border-opacity-100 group-active:bg-opacity-100 group-hover:text-gray-100 group-focus:border-opacity-100 transition-border-background ease-in-out duration-quick"
    >
        <div class="text-gray-200 {{ $capitalize ? 'capitalize' : '' }} {{ $textClasses }} transform group-hover:scale-105 transition-transform ease-in-out duration-quick">
            @isset($icon)
                <x-icon class="mr-2">{{ $icon }}</x-icon>
            @endisset
            {{ $slot }}
        </div>
    </div>
</a>
