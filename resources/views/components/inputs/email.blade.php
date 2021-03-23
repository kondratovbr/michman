@props(['showErrors' => true])

<x-inputs.text {{ $attributes->merge([
    'type' => 'email',
    'autocomplete' => 'email',
]) }}
    :showErrors="$showErrors"
    widthClass="max-w-md"
>
    @isset($iconLeft)
        <x-slot name="iconLeft">
            {{ $iconLeft }}
        </x-slot>
    @else
        <x-slot name="iconLeft">
            <i class="far fa-envelope"></i>
        </x-slot>
    @endisset
</x-inputs.text>
