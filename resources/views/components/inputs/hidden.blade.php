@props(['name', 'value'])

<input
    {{ $attributes }}
    type="hidden"
    name="{{ $name }}"
    value="{{ $value }}"
    {{-- Needed for Tailwinds "space-X-X" utilities to work - they rely on the "hidden" property. --}}
    hidden
    {{-- Needed for Alpines focusables() logic to work on modals. --}}
    disabled
>
