@props(['colors', 'loading' => false])

<div {{ $attributes->class([
    'inline-flex justify-center items-center whitespace-nowrap select-none',
    'h-8-em px-2 rounded-xl',
    'text-xs font-extrabold uppercase leading-normal',
    match ($colors ?? null) {
        'info'      => 'bg-navy-500 text-blue-100',
        'success'   => 'bg-green-800 text-green-100',
        'warning'   => 'bg-yellow-400 text-yellow-900',
        'danger'    => 'bg-red-700 text-red-100',
        'none'      => '',
        default     => 'bg-gray-700 text-gray-100',
    },
]) }}
>
    @if($loading)
{{--        TODO: CRITICAL! This is a temporary spinner. The actual one is "fad fa-spinner-third" and is found in a paid FontAwesome version. I should pay for it and use it. I also have some other places that require paid FA icons. And check if the wobble is too big - FA has an SVG+JS version that they say helps with it.--}}
        <x-icon class="block -ml-1 mr-1"><i class="block fas fa-spinner fa-spin"></i></x-icon>
    @endif
    <span>{{ $slot }}</span>
</div>
