@props(['loading' => false])

@php
    $classes = implode(' ', [
        'inline-flex items-center justify-center rounded-md outline-none cursor-pointer select-none whitespace-nowrap',
        'py-1.5 px-4',
        'bg-red-600 text-gray-100',
        'hover:bg-red-700',
        'active:bg-red-800',
        'focus:bg-red-700 focus:ring-red-500 focus:outline-none focus:ring focus:ring-opacity-50',
        'disabled:bg-red-600 disabled:opacity-50 disabled:cursor-default',
        'transition-ring-background ease-in-out duration-quick',
        'border-2 border-transparent',
    ]);
@endphp

<button
    {{ $attributes->merge(['class' => $classes, 'type' => 'button']) }}
    x-data="{ loading: {{ $loading ? 'true' : 'false' }} }"
    x-bind:disabled="loading"
    x-on:click="loading = true"
>
    <div
        class="justify-center items-center"
        x-bind:class="{ 'flex': loading, 'hidden': ! loading}"
        x-cloak
    >
        <x-icon><i class="block fas fa-spinner fa-spin"></i></x-icon>
    </div>
    <div
        class="justify-center items-center"
        x-bind:class="{ 'flex': ! loading, 'hidden': loading}"
    >
        <x-icon><i class="far fa-trash-alt"></i></x-icon>
    </div>
</button>
