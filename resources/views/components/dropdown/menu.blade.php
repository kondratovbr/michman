<div
    class="absolute z-50 {{ $marginClasses }} {{ $widthClass }} rounded-md border border-gray-600 shadow-lg {{ $alignmentClasses }}"
    x-show="{{ $show }}"
    x-cloak
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="transform opacity-0 scale-95"
    x-transition:enter-end="transform opacity-100 scale-100"
    x-transition:leave="transition ease-in duration-75"
    x-transition:leave-start="transform opacity-100 scale-100"
    x-transition:leave-end="transform opacity-0 scale-95"
>
    <div class="rounded-md py-2 bg-navy-300">
        {{ $slot }}
    </div>
</div>
