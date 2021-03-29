<x-inputs.text {{ $attributes->merge([
    'type' => 'password',
    'autocomplete' => 'current-password',
    'minlength' => (string) config('auth.password.min_length'),
    'maxlength' => (string) config('auth.password.max_length'),
]) }}
    widthClass="max-w-md"
>
    @isset($iconLeft)
        <x-slot name="iconLeft">{{ $iconLeft }}</x-slot>
    @else
        <x-slot name="iconLeft"><i class="fa fa-lock"></i></x-slot>
    @endisset
</x-inputs.text>
