<button
    {{ $attributes->merge([
        'type' => 'submit',
        'class' => 'inline-flex items-center px-4 py-2 bg-gold border border-transparent rounded-md font-semibold text-xs text-gray-900 uppercase tracking-widest hover:bg-gold-dark active:bg-gold-darkest focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition ease-in-out duration-150',
    ]) }}
>{{ $slot }}</button>
