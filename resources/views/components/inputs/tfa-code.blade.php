<x-inputs.text {{ $attributes->merge([
    'autocomplete' => 'one-time-code',
    'inputmode' => 'numeric',
    'minlength' => 6,
    'maxlength' => 6,
]) }}
    widthClass="max-w-3xs"
>
    @isset($iconLeft)
        <x-slot name="iconLeft">{{ $iconLeft }}</x-slot>
    @else
        <x-slot name="iconLeft">
            <i class="far fa-clock"></i>
        </x-slot>
    @endisset
</x-inputs.text>
