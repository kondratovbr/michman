<span {{ $attributes->merge([
    'class' => $sizeClasses . ' '  . 'inline-flex items-center justify-center',
]) }}>
    {{ $slot }}
</span>
