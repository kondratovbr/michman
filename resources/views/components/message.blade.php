@props(['colors'])

<div {{ $attributes->class([
    'py-5 px-6 rounded-lg',
    match ($colors ?? null) {
        'info'      => 'bg-navy-500 text-blue-100',
        'success'   => 'bg-green-800 text-green-100',
        'warning'   => 'bg-yellow-400 text-yellow-900',
        'danger'    => 'bg-red-700 text-red-100',
        'none'      => '',
         default    => 'bg-gray-700 text-gray-100',
    },
]) }}>
    {{ $slot }}
</div>
