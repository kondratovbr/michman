@props(['name'])

<div {{ $attributes->merge([
    'class' => 'space-y-10 sm:space-y-0',
]) }}
    x-data="{ show: false }"
    x-show.transition.in.duration.500ms.origin.top.left.opacity.scale.95="show"
    x-init="
        setTimeout(() => { show = true });
        $dispatch('sub-page-shown', { page: '{{ $name }}' });
    "
>
    {{ $slot }}
</div>
