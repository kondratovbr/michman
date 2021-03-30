<div {{ $attributes->merge(['class' => classes(
    'absolute z-50 max-w-screen',
    'rounded-md border border-gray-600',
    $marginClasses,
    $widthClass,
    $alignmentClasses,
    $shadowClass,
)]) }}
    x-show="{{ $show }}"
    x-cloak
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="transform opacity-0 scale-95"
    x-transition:enter-end="transform opacity-100 scale-100"
    x-transition:leave="transition ease-in duration-75"
    x-transition:leave-start="transform opacity-100 scale-100"
    x-transition:leave-end="transform opacity-0 scale-95"
>
    <div class="rounded-md py-0 md:py-2 bg-navy-300">
        {{ $slot }}
    </div>
</div>
