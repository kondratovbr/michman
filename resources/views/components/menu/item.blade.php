<li
    {{-- This compensates for the padding on the first and last elements. --}}
    class="first:-mt-1 last:-mb-1"
>
    <a
        class="group w-full h-full inline-flex flex-col items-stretch py-1 cursor-pointer select-none focus:outline-none"
        @isset($show)
            x-on:click="show = false"
            wire:click.prefetch="show('{{ $show }}')"
        @endisset
    >
        <div
            class="capitalize flex items-center py-3 px-4 rounded-lg border border-gray-300 border-opacity-0 bg-navy-300 group-focus:border-opacity-100 {{ $buttonStateClasses }} transition-border-background ease-in-out duration-quick"
        >
            <div class="md:text-sm lg:text-base transform {{ $contentStateClasses }} transition-transform ease-in-out duration-quick">
                @isset($icon)
                    <x-icon class="mr-2">{{ $icon }}</x-icon>
                @endisset
                <span>{{ $slot }}</span>
            </div>
        </div>
    </a>
</li>
