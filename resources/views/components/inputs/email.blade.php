<x-inputs.text {{ $attributes->merge([
    'type' => 'email',
    'autocomplete' => 'email',
]) }}>
    <x-slot name="iconLeft">
        <i class="fa fa-envelope"></i>
    </x-slot>
</x-inputs.text>
