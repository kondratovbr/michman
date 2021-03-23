@props(['name', 'value'])

<input
    {{ $attributes }}
    type="hidden"
    name="{{ $name }}"
    value="{{ $value }}"
    {{-- Needed for Tailwinds "space-X-X" utilities to work as well as for
    Alpines FocuableDialog component - they rely on the "hidden" property. --}}
    hidden
>
