@props(['completed' => false])

<div {{ $attributes->class([
    'flex items-center justify-center h-8 w-8 rounded-full border-2 border-navy-100 text-center',
    'text-md font-medium',
    $completed ? 'bg-green-500 text-gray-200' : 'bg-gray-200 text-gray-900',
]) }}> ✓ </div>
