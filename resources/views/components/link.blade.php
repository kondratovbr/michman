<a {{ $attributes->merge([
    'class' => 'cursor-pointer underline text-gray-300 hover:text-gray-200 active:text-gray-100 transition-text ease-in-out duration-quick',
]) }}>{{ $slot }}</a>
