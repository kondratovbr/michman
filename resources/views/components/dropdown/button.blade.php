{{--TODO: Refactor this so icon is easier to set (not a whole slot, like now, just a single parameter - icon class, like "fas fa-trash". Check every place I have this thing.--}}

@props(['capitalize' => true, 'textClasses' => '', 'external' => false])

<button
    {{ $attributes->class([
        'group h-full w-full flex flex-col items-stretch cursor-pointer select-none focus:outline-none',
        'px-3 md:px-2 py-1',
        'rounded-t-md bg-navy-300 border-b border-gray-600 md:border-b-0',
        'disabled:cursor-default disabled:opacity-50',
    ]) }}
    x-on:click="open = false"
>
    <div
        class="py-3 px-4 rounded-md border border-gray-300 border-opacity-0 bg-navy-400 bg-opacity-0 group-hover:border-opacity-100 group-active:bg-opacity-100 group-hover:text-gray-100 group-focus:border-opacity-100 transition-border-background ease-in-out duration-quick group-disabled:border-opacity-0 group-disabled:bg-opacity-0"
    >
        <div
            class="flex items-center justify-start text-gray-200 {{ $capitalize ? 'capitalize' : '' }} {{ $textClasses }} transform group-hover:scale-105 transition-transform ease-in-out duration-quick group-disabled:scale-100"
        >
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
</button>
