<x-inputs.text {{ $attributes->merge([
    'type' => 'password',
    'autocomplete' => 'current-password',
]) }}>
    <x-slot name="iconLeft"><i class="fa fa-lock"></i></x-slot>
</x-inputs.text>
