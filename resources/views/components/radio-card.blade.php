@props(['name', 'value', 'checked' => false])

<div class="relative w-32 h-32">

    <input
        type="radio"
        class="input sibling radio absolute top-3 left-3 z-10 checked:bg-gold-800 text-black dot-gray-900 cursor-pointer"
        name="{{ $name }}"
        id="{{ $name . '-' . $value }}"
        value="{{ $value }}"
        @if($checked)
            checked
        @endif
    >

    <label
        class="absolute inset-0 border rounded-lg px-6 py-4 bg-opacity-0 hover:bg-navy-400 hover:bg-opacity-100 sibling-hover:bg-navy-400 sibling-hover:bg-opacity-100 active:bg-navy-500 active:bg-opacity-100 input-checked:border-gold-800 input-checked:ring-2 input-checked:ring-inset input-checked:ring-gold-800 input-checked:shadow-lg cursor-pointer select-none transition-all ease-in-out duration-quick"
        for="{{ $name . '-' . $value }}"
    >

        @isset($content)
            <div class="w-full h-full flex flex-col items-center justify-center space-y-2">
                {{ $content }}
            </div>
        @endisset

        {{ $slot }}

    </label>

</div>
