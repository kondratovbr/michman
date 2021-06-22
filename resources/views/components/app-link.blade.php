<a {{ $attributes->class([
    'cursor-pointer font-bold text-gold-800',
    'hover:text-gold-700 hover:underline',
    'active:text-gold-500',
]) }}>
    {{ $slot }}
</a>
