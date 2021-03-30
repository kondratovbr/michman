@props(['align' => 'left'])

<div {{ $attributes->merge([
    'class' => 'relative',
]) }}
    x-data="{ open: true }"
    {{-- TODO: IMPORTANT! Does this work on touch? --}}
    x-on:click.away="open = false"
    x-on:close.stop="open = false"
>

    {{-- Dropup button --}}
    <x-bottombar.link
        {{-- TODO: IMPORTANT! Does this work on touch? --}}
        x-on:click.prevent="open = !open"
        role="button"
    >
        {{ $trigger }}
    </x-bottombar.link>

    {{-- Dropup menu --}}
    <x-dropdown.menu drop="up" :align="$align">
        {{ $slot }}
    </x-dropdown.menu>

</div>
