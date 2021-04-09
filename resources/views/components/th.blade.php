@props(['align' => 'left'])

<th {{ $attributes->class([
    'py-4 px-6 font-bold',
    /*font-bold uppercase text-sm text-grey-dark*/
]) }}>
    <div class="flex {{ $align === 'center' ? 'justify-center' : '' }} {{ $align === 'right' ? 'justify-end' : '' }}">
        {{ $slot }}
    </div>
</th>
