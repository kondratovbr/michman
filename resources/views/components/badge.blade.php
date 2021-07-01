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
        <x-spinner class="-ml-1 mr-1" />
    @endif
    <span>{{ $slot }}</span>
</div>
