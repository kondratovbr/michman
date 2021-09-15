@props(['external' => false, 'icon' => true])

<a {{ $attributes->class([
    'cursor-pointer font-bold text-gold-800',
    'hover:text-gold-700 hover:underline',
    'active:text-gold-500',
]) }}
    @if($external)
        target="_blank"
    @endif
>
    <span>{{ $slot }}</span>
    @if($external && $icon)
        <x-icon><i class="fas fa-external-link-alt"></i></x-icon>
    @endif
</a>
