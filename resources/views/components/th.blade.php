@props(['align' => 'left', 'show' => null])

<th {{ $attributes->class([
    'px-table-cell py-2 sm:py-3 lg:py-4',
    'font-bold',
    // Rounding here should match the rounding in box.blade.php.
    'sm:first:rounded-tl-lg sm:last:rounded-tr-lg',
    match ($show) {
        'sm' => 'hidden sm:table-cell',
        'md' => 'hidden md:table-cell',
        'lg' => 'hidden lg:table-cell',
        'xl' => 'hidden xl:table-cell',
        '2xl' => 'hidden 2xl:table-cell',
        default => '',
    },
]) }}>
    <div class="flex {{ $align === 'center' ? 'justify-center' : '' }} {{ $align === 'right' ? 'justify-end' : '' }}">
        {{ $slot }}
    </div>
</th>
