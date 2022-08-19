{{-- TODO: Update this so I can put badges/buttons inside and have them properly aligned. --}}

@props(['show' => null])

<td {{ $attributes->class([
    'px-table-cell py-2 sm:py-3 lg:py-3 first:rounded-l-lg last:rounded-r-lg',
    match ($show) {
        'sm' => 'hidden sm:table-cell',
        'md' => 'hidden md:table-cell',
        'lg' => 'hidden lg:table-cell',
        'xl' => 'hidden xl:table-cell',
        '2xl' => 'hidden 2xl:table-cell',
        default => '',
    },
]) }}>
    {{ $slot }}
</td>
