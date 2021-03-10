{{--TODO: IMPORTANT! Does it work on touch? Is it supposed to?--}}
{{--TODO: Needs hover, focus. Also, a11y concerns for dropdowns.--}}

<div
    class="relative"
    x-data="{ open: false }"
    @click.away="open = false"
    @close.stop="open = false"
>
    <div
        class="h-full flex items-center"
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
        class="absolute z-50 mt-2 {{ $widthClass }} rounded-md shadow-lg {{ $alignmentClasses }}"
        style="display: none;"
    >
        <div class="rounded-md ring-1 ring-black ring-opacity-5 py-1 bg-white">
            {{ $content }}
        </div>
    </div>
</div>
