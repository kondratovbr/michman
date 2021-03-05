@props(['name', 'id'])

<input
    {{ $attributes->merge([
        'class' => 'block border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm',
        'value' => old($name),
    ]) }}
    name="{{ $name }}"
    id="{{ $id ?? $name }}"
>
