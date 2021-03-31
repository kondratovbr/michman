@props(['capitalize' => true, 'textClasses' => '', 'external' => false])

<a {{ $attributes->merge([
    'class' => 'group h-full flex flex-col items-stretch px-6 md:px-2 py-1 cursor-pointer select-none rounded-t-md focus:outline-none bg-navy-200     border-b border-gray-600 md:border-b-0',
]) }}
    @isset($external)
        target="_blank"
    @endisset
>
    <div
        class="py-3 px-4 rounded-md border border-gray-300 border-opacity-0 bg-navy-400 bg-opacity-0 group-hover:border-opacity-100 group-active:bg-opacity-100 group-hover:text-gray-100 group-focus:border-opacity-100 transition-border-background ease-in-out duration-quick"
    >
        <div class="flex items-center justify-start text-gray-200 {{ $capitalize ? 'capitalize' : '' }} {{ $textClasses }} transform group-hover:scale-105 transition-transform ease-in-out duration-quick">
            <div>
                @isset($icon)
                    <x-icon class="mr-2">{{ $icon }}</x-icon>
                @endisset
                {{ $slot }}
            </div>
            @isset($iconRight)
                <x-icon class="ml-2">{{ $iconRight }}</x-icon>
            @endisset
        </div>
    </div>
</a>
