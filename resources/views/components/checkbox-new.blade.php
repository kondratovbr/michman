<label
    class="group -ml-2 py-1 px-2 inline-flex items-center cursor-pointer select-none rounded border border-gray-300 border-opacity-0 bg-navy-400 bg-opacity-0 hover:border-opacity-100 hover:text-gray-100 active:bg-opacity-100 transition-border-background ease-in-out duration-quick"
    for="{{ $id ?? $name }}"
>
    <input
        type="checkbox"
        class="checkbox cursor-pointer rounded border-gray-300 border-opacity-100 bg-transparent tick-gray-900 checked-bg-gold-800 checked-border-opacity-0"
        id="{{ $id ?? $name }}"
        name="{{ $name }}"
        @if(old($name) == 'on')
            checked
        @endif
    >
    <span class="ml-2 text-sm group-hover:text-gray-100">
        {{ $slot }}
    </span>
</label>
