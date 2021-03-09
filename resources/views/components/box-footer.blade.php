<div
    {{ $attributes->merge([
        'class' => 'flex items-center px-4 py-3 bg-navy-200 sm:px-6 sm:rounded-b-lg',
    ]) }}
>
    {{ $slot }}
</div>
