@props(['name', 'id', 'showErrors' => true, 'errorName'])

<textarea {{ $attributes->class([
    'block w-full placeholder-opacity-50 bg-transparent border-2 rounded-md shadow-sm',
    'focus:ring focus:ring-opacity-50',
    'disabled:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed',
    ($showErrors && $errors->has($errorName ?? $name))
        ? 'border-red-600 hover:border-red-500 focus:border-red-500 focus:ring-red-300 disabled:border-red-600'
        : 'border-gray-400 hover:border-gray-300 focus:border-gray-300 focus:ring-indigo-200 disabled:border-gray-400',
    'transition-border-ring ease-in-out duration-quick',
])->merge([
    'name' => $name,
    'id' => $id ?? $name,
    'cols' => '30',
    'rows' => '10',
]) }}>{{ $slot ?? old($name) }}</textarea>
