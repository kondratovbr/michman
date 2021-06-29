{{--TODO: IMPORTANT! Does it work on touch? Is it supposed to?--}}
{{--TODO: Maybe figure out how to highlight the activator button when the dropdown is open somehow.--}}

@props(['disabled' => false])

<div
    {{ $attributes->class([
        'inline-block relative',
    ]) }}
    x-data="{ open: false }"
    {{-- TODO: IMPORTANT! Does this work on touch? --}}
    x-on:click.outside="open = false"
    x-on:close.stop="open = false"
>
    <x-buttons.ellipsis
{{--        TODO: CRITICAL! Check how it looks and works in Safari. It had a bug with sizing button contents that's why I had a as link ("a" tag) instead of a button.--}}
{{--        :link="true"--}}
        {{-- TODO: IMPORTANT! Does this work on touch? --}}
        x-on:click.prevent="open = !open"
        role="button"
        :disabled="$disabled"
    />

    {{ $slot }}

</div>
