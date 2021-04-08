@props(['name', 'id', 'showErrors' => true, 'errorName'])

<input
    {{ $attributes->class([
        'block w-full placeholder-opacity-50 bg-navy-300 border-2 rounded-md shadow-sm',
        'focus:ring focus:ring-opacity-50',
        ($showErrors && $errors->has($errorName ?? $name))
            ? 'border-red-600 hover:border-red-500 focus:border-red-500 focus:ring-red-300'
            : 'border-gray-400 hover:border-gray-300 focus:border-gray-300 focus:ring-indigo-200',
        'transition-border-ring ease-in-out duration-quick',
    ])->merge([
        'value' => old($name),
        'type' => 'text',
        'name' => $name,
        'id' => $id ?? $name,
    ]) }}
>
