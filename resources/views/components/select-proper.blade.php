@props(['name', 'options' => [], 'placeholder' => ' ', 'wireModelChangedEvent', 'wireOptionsChangedEvent', 'labelId'])

<div
    class="relative"
    x-data="selectProper({
        @alpine($options, $name, $placeholder),
        @if($attributes->wire('model') != '')
            'wireModel': '{{ $attributes->wire('model')->value() }}',
            'wireEvent': '{{ $wireModelChangedEvent ?? $attributes->wire('model')->value() . '-changed' }}',
            @isset($wireOptionsChangedEvent)
                'wireOptionsEvent': '{{ $wireOptionsChangedEvent }}',
            @endisset
        @endif
    })"
    x-init="init()"
    x-on:click.away="closeListbox(false)"
    x-on:keydown.escape="closeListbox()"
>
    {{-- Activation button --}}
    <button
        class="{{ classes(
            'relative w-full py-2 pl-3 pr-10 rounded-md cursor-pointer select-none',
            'bg-navy-300 border-2 border-gray-400 ring ring-transparent ring-opacity-0',
            'focus:outline-none focus-within:border-gray-300 focus-within:ring-opacity-50 focus-within:ring-indigo-200',
            'transition duration-quick ease-in-out',
        ) }}"
        type="button"
        x-ref="button"
        x-on:click.prevent="toggleListboxVisibility()"
        x-bind:aria-expanded="open"
        @isset($labelId)
            aria-labelledby="{{ $labelId }}"
        @endisset
        aria-haspopup="listbox"
        x-on:keydown.enter.stop.prevent="selectOption()"
        x-on:keydown.arrow-up.prevent="focusPreviousOption()"
        x-on:keydown.arrow-down.prevent="focusNextOption()"
        x-on:keydown.tab="closeListbox()"
    >
        {{-- Name of a currently chosen option or a placeholder --}}
        <div
            class="w-full h-full min-h-6-em truncate text-left pointer-events-none select-none"
            x-text="value in options ? options[value] : placeholder"
            x-bind:class="{ 'text-gray-500': !(value in options) }"
        ></div>

        {{-- Icon for the activation button --}}
        <span class="absolute inset-y-0 right-0 ml-3 flex items-center pr-2 pointer-events-none select-none">
            <x-heroicons.solid.selector
                class="w-5 h-5 text-gray-400"
                x-bind:class="{
                    'text-gray-400' : ! open,
                    'text-gray-300' : open,
                }"
            />
        </span>
    </button>

    {{-- Dropdown menu --}}
    <div
        class="absolute z-10 w-full mt-2 bg-navy-400 border border-gray-600 rounded-md shadow-lg origin-top"
        x-show="open"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
    >
        <ul
            class="py-1 w-full max-h-60 overflow-auto text-base leading-6 max-h-60 focus:outline-none"
            x-ref="listbox"
            x-on:keydown.enter.stop.prevent="selectOption()"
            x-on:keydown.arrow-up.prevent="focusPreviousOption()"
            x-on:keydown.arrow-down.prevent="focusNextOption()"
            role="listbox"
            @isset($labelId)
                aria-labelledby="{{ $labelId }}"
            @endisset
            x-bind:aria-activedescendant="focusedOptionIndex ? name + 'Option' + focusedOptionIndex : null"
            tabindex="-1"
        >
            <template x-for="(key, index) in Object.keys(options)" :key="index">
                <li
                    class="relative py-2 pl-3 pr-9 cursor-pointer select-none"
                    x-bind:id="name + 'Option' + index"
                    x-on:click="selectOption()"
                    x-on:mouseenter="focusedOptionIndex = index"
                    x-on:mouseleave="focusedOptionIndex = null"
                    x-bind:aria-selected="focusedOptionIndex === index"
                    x-bind:class="{
                        'text-gray-300 bg-navy-500': index === focusedOptionIndex,
                        'text-gray-100': index !== focusedOptionIndex
                    }"
                    role="option"
                >
                    {{-- Name of an option --}}
                    <span
                        class="block truncate"
                        x-text="Object.values(options)[index]"
                        x-bind:class="{
                            'font-semibold': index === focusedOptionIndex,
                            'font-normal': index !== focusedOptionIndex
                        }"
                    ></span>

                    {{-- Checkmark icon for a selected option --}}
                    <span
                        class="absolute inset-y-0 right-0 items-center pr-4"
                        x-bind:class="{
                            'flex': key === value,
                            'hidden': key !== value,
                        }"
                    >
                        <x-icon><i class="fas fa-check"></i></x-icon>
                    </span>
                </li>
            </template>
        </ul>
    </div>

</div>
