<x-inputs.text {{ $attributes->merge([
    'type' => 'password',
    'autocomplete' => 'current-password',
]) }}>
    @isset($iconLeft)
        <x-slot name="iconLeft">{{ $iconLeft }}</x-slot>
    @else
        <x-slot name="iconLeft"><i class="fa fa-lock"></i></x-slot>
    @endisset
</x-inputs.text>
