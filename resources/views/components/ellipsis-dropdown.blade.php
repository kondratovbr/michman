{{--TODO: VERY IMPORTANT! Does it work on touch? Is it supposed to?--}}
{{--TODO: Maybe figure out how to highlight the activator button when the dropdown is open somehow.--}}
{{--TODO: IMPORTANT! My dropdowns (other as well) are a bit stupid - they drop down even when they're at the bottom of the screen. Should drop up. Maybe try some JS library with dropdowns? Like maybe even Tippy? Or just google some. Aim for no-dependency and lightweight. Does "Alpine Components" projects has it?--}}

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
{{--        :link="true"--}}
        x-on:click.prevent="open = !open"
        role="button"
        :disabled="$disabled"
    />

    {{ $slot }}

</div>
