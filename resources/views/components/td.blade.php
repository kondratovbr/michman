{{-- TODO: Update this so I can put badges/buttons inside and have them properlu aligned. --}}

@props(['mobile' => true])

<td {{ $attributes->class([
    'py-3 px-6',
    ($mobile ?? true) ? '' : 'hidden md:table-cell',
]) }}>
    {{ $slot }}
</td>
