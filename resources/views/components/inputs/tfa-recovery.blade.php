<x-inputs.text {{ $attributes->merge([
    'autocomplete' => 'one-time-code',
    //'minLength' => 6,
    //'maxLength' => 6,
]) }}
    widthClass="max-w-xs"
>
    @isset($iconLeft)
        <x-slot name="iconLeft">{{ $iconLeft }}</x-slot>
    @else
        <x-slot name="iconLeft">
            <i class="fas fa-undo-alt"></i>
        </x-slot>
    @endisset
</x-inputs.text>
