{{--TODO: IMPORTANT! Unfinished! Active state, hover, check colors. Optimize transition. Maybe make faster. --}}

@props(['name', 'id'])

<input
    {{ $attributes->merge([
        'class' => 'block w-full bg-navy-300 border-2 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm transition-border-ring ease-in-out duration-quick' . ' ' . ($errors->has($name) ? 'border-red-500' : 'border-gray-400'),
        'value' => old($name),
        'type' => 'text',
    ]) }}
    name="{{ $name }}"
    id="{{ $id ?? $name }}"
>
