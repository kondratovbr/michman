@props(['align' => 'left'])

<th {{ $attributes->class([
    // Rounding here should match the rounding in box.blade.php.
    'py-4 px-6 font-bold sm:first:rounded-tl-lg sm:last:rounded-tr-lg',
]) }}>
    <div class="flex {{ $align === 'center' ? 'justify-center' : '' }} {{ $align === 'right' ? 'justify-end' : '' }}">
        {{ $slot }}
    </div>
</th>
