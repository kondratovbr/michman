{{--TODO: Needs active state.--}}

<a {{ $attributes->merge([
    'class' => 'block px-4 py-3 text-gray-200 text-sm leading-5 hover:bg-navy-400 hover:text-gray-100 focus:outline-none focus:bg-gray-100 transition duration-100 ease-in-out'
]) }}>{{ $slot }}</a>
