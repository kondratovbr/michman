{{--TODO: IMPORTANT! Does it work on touch? Is it supposed to?--}}
{{--TODO: Needs hover, focus. Also, a11y concerns for dropdowns.--}}
{{--TODO: Needs different styling for links for when user is on that page. --}}

<div
    class="relative"
    x-data="{ open: false }"
    x-on:click.away="open = false"
    x-on:close.stop="open = false"
>
{{--If a button is used here - in Safari the child div (inside the button) doesn't stretch vertically by any means. A bug in safari.--}}
    <a
        class="group py-2 h-full w-full flex items-stretch cursor-pointer focus:outline-none"
        x-on:click="open = !open"
        role="button"
    >
        <div
            class="px-5 rounded-md flex items-center self-stretch text-sm select-none border-2 border-gray-300 border-opacity-0 group-hover:border-opacity-100 bg-navy-400 group-active:bg-opacity-100 transition-border-background ease-in-out duration-quick"
            x-bind:class="{'bg-opacity-0': !open, 'bg-opacity-100': open}"
        >
            <div class="flex items-center transform group-hover:scale-105 transition-transform ease-in-out duration-quick">
                {{ $trigger }}
                <x-icon class="ml-2">
                    <i
                        class="fa"
                        x-bind:class="{'fa-chevron-down': !open, 'fa-times': open}"
                    ></i>
                </x-icon>
            </div>
        </div>
    </a>

    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="absolute z-50 -mt-1 {{ $widthClass }} rounded-md border border-gray-600 shadow-lg-black {{ $alignmentClasses }}"
        style="display: none;"
    >
        <div class="rounded-md py-2 bg-navy-300">
            {{ $content }}
        </div>
    </div>
</div>
