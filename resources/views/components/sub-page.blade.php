<div {{ $attributes->merge([
    'class' => 'space-y-10 sm:space-y-0',
]) }}
    wire:key="{{ $name }}"
    x-data="{ show: false }"
    x-show.transition.duration.500ms="show"
    x-init="setTimeout(() => { show = true })"
>
    {{ $slot }}
</div>
