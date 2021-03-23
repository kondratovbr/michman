<x-inputs.text {{ $attributes->merge([
    'type' => 'password',
    'autocomplete' => 'current-password',
]) }}
    widthClass="max-w-md"
>
    @isset($iconLeft)
        <x-slot name="iconLeft">{{ $iconLeft }}</x-slot>
    @else
        <x-slot name="iconLeft"><i class="far fa-lock"></i></x-slot>
    @endisset
</x-inputs.text>
