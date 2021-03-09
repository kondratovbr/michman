<li>
    <a
        {{ $attributes->merge([
            'class' => 'inline-block w-full py-3 px-4 rounded-lg hover:ring-2 hover:ring-inset hover:ring-gray-300 active:bg-navy-200 cursor-pointer select-none',
        ]) }}
    >{{ $slot }}</a>
</li>
