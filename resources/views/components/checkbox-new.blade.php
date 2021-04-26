{{--TODO: Figure out disabled state.--}}

@props(['id', 'name', 'defaultState' => 'off', 'labelClass' => ''])

<label
    class="group -ml-2 py-1 px-2 inline-flex items-center cursor-pointer select-none rounded border border-gray-300 border-opacity-0 bg-navy-400 bg-opacity-0 hover:border-opacity-100 hover:text-gray-100 active:bg-opacity-100 focus:outline-none transition-border-background ease-in-out duration-quick"
    for="{{ $id ?? $name }}"
>
    <input
        type="checkbox"
        class="checkbox cursor-pointer rounded border-gray-300 border-opacity-100 bg-transparent checked-bg-gold-800 tick-gray-900 checked-border-opacity-0 focus:outline-none"
        id="{{ $id ?? $name }}"
        name="{{ $name }}"
        @if(old($name, $defaultState) === 'on')
            checked
        @endif
        {{ $attributes->wire('model') }}
{{--        disabled--}}
    >
    <span class="ml-2 group-hover:text-gray-100 {{ $labelClass }}">
        {{ $slot }}
    </span>
</label>
