@props(['align' => 'left', 'minWidth' => '48'])

<div {{ $attributes->merge([
    'class' => 'relative',
]) }}
    x-data="{ open: false, sub: '' }"
    {{-- TODO: IMPORTANT! Does this work on touch? --}}
    x-on:click.away="open = false; sub = ''"
    x-on:close.stop="open = false; sub = ''"
>

    {{-- Dropup button --}}
    <x-bottombar.link
        {{-- TODO: IMPORTANT! Does this work on touch? --}}
        x-on:click.prevent="open = !open; sub = ''"
        role="button"
    >
        {{ $trigger }}
    </x-bottombar.link>

    {{-- Dropup menus --}}
    {{ $slot }}

</div>
