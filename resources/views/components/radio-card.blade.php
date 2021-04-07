{{--TODO: Figure out disabled state.--}}

<div {{ $attributes->class(['relative'])->only('class') }}>

    <input
        {{ $attributes->merge([
            'type' => 'radio',
            'name' => $name,
            'id' => $attributes->get('name') . '-' . $attributes->get('value'),
            'value' => $value,
        ])->except('class') }}
        class="input sibling radio absolute top-3 left-3 z-10 checked:bg-gold-800 text-black dot-gray-900 cursor-pointer"
{{--        type="radio"--}}
{{--        name="{{ $name }}"--}}
{{--        id="{{ $name . '-' . $value }}"--}}
{{--        value="{{ $value }}"--}}
{{--        @if($checked)--}}
{{--            checked--}}
{{--        @endif--}}
{{--        disabled--}}
    >

    <label
        class="{{ classes(
            'absolute inset-0 cursor-pointer select-none',
            'px-6 py-4',
            'border rounded-lg bg-opacity-0 ring-2 ring-inset ring-gold-800 ring-opacity-0',
            'hover:bg-navy-400 hover:bg-opacity-100',
            'active:bg-navy-500 active:bg-opacity-100',
            'input-checked:border-gold-800 input-checked:ring-opacity-100 input-checked:shadow-lg',
            'transition-ring-background ease-in-out duration-quick',
        ) }}"
        for="{{ $name . '-' . $value }}"
    >

        @isset($content)
            <div class="w-full h-full flex flex-col items-center justify-center">
                {{ $content }}
            </div>
        @endisset

        {{ $slot }}

    </label>

</div>
