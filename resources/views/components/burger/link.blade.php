@props(['active'])

@php
    $classes = ($active ?? false)
        ? 'block pl-3 pr-6 py-2 border-l-4 border-indigo-400 text-base font-medium bg-navy-500 focus:outline-none focus:bg-indigo-100 focus:border-indigo-700 transition duration-quick ease-in-out'
        : 'block pl-3 pr-6 py-2 border-l-4 border-transparent text-base font-medium hover:bg-navy-400 hover:border-gray-300 focus:outline-none focus:bg-gray-50 focus:border-gray-300 transition duration-quick ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    @isset($icon)
        <x-icon class="mr-2">{{ $icon }}</x-icon>
    @endisset
    {{ $slot }}
</a>
