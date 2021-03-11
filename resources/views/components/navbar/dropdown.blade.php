{{--TODO: IMPORTANT! Does it work on touch? Is it supposed to?--}}
{{--TODO: Needs hover, focus. Also, a11y concerns for dropdowns.--}}
{{--TODO: Needs different styling for links for when user is on that page. --}}

<div
    class="relative"
    x-data="{ open: false }"
    @click.away="open = false"
    @close.stop="open = false"
>
    <div
        class="h-full pt-1 flex items-center"
        @click="open = ! open"
    >
        {{ $trigger }}
    </div>

    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="absolute z-50 -mt-2 {{ $widthClass }} rounded-md border border-gray-600 shadow-lg-black {{ $alignmentClasses }}"
        style="display: none;"
    >
        <div class="rounded-md py-2 bg-navy-300">
            {{ $content }}
        </div>
    </div>
</div>
