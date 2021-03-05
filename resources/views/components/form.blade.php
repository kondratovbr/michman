<form
    {{ $attributes->merge([
        'class' => 'space-y-4',
    ]) }}
>
    <x-csrf/>

    {{ $slot }}

</form>
