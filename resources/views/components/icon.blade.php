{{--TODO: Implement icon sizing. See how Bulma does it.--}}

<span {{ $attributes->merge([
    'class' => 'w-4 inline-flex items-center justify-center',
]) }}>
    {{ $slot }}
</span>
