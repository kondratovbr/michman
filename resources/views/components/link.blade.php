@props(['external' => false, 'icon' => true])

<a {{ $attributes->class([
    'cursor-pointer underline text-gray-300',
    'hover:text-gray-200',
    'active:text-gray-100',
    'transition-text ease-in-out duration-quick',
]) }}
    @if($external)
        target="_blank"
    @endif
>
    {{ $slot }}
    @if($external && $icon)
        <x-icon><i class="fas fa-external-link-alt"></i></x-icon>
    @endif
</a>
