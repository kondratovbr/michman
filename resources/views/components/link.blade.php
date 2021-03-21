<a {{ $attributes->merge([
    'class' => 'underline text-gray-400 hover:text-gray-100 active:text-gray-50 transition-text ease-in-out duration-quick',
]) }}>{{ $slot }}</a>
