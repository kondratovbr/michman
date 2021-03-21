{{--TODO: IMPORTANT! Unfinished! Active state, hover, check colors. Optimize transition. Maybe make faster. --}}

@props(['name', 'id', 'showErrors' => true])

<input
    {{ $attributes->merge([
        'class' => 'block w-full placeholder-opacity-50 bg-navy-300 border-2 focus:ring focus:ring-opacity-50 rounded-md shadow-sm transition-border-ring ease-in-out duration-quick' . ' '
        . (($showErrors && $errors->has($name))
            ? 'border-red-600 focus:ring-red-300'
            : 'border-gray-400 focus:ring-indigo-200'
        ),
        'value' => old($name),
        'type' => 'text',
    ]) }}
    name="{{ $name }}"
    id="{{ $id ?? $name }}"
>
