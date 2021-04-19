{{--TODO: Does it need something changed for smaller screens?--}}
{{--TODO: IMPORTANT! Make sure it us usable on touch.--}}
{{--TODO: Google some a11y guide for such menus and make sure we're alright.--}}

<div
    x-data="searchSelect({
        data: {
            'au': 'Australia',
            'be': 'Belgium',
            'cn': 'China',
            'fr': 'France',
            'de': 'Germany',
            'it': 'Italy',
            'mx': 'Mexico',
            'es': 'Spain',
            'tr': 'Turkey',
            'gb': 'United Kingdom',
        },
        emptyOptionsMessage: 'No countries match your search.',
        name: 'country',
        placeholder: 'Select a country'
    })"
    x-init="init()"
    x-on:click.away="closeListbox()"
    x-on:keydown.escape="closeListbox()"
    class="relative"
>

    {{-- Activation button --}}
    <div>
        <button
            class="{{ classes(
                'relative w-full py-2 pl-3 pr-10 rounded-md cursor-default',
                'bg-navy-300 border-2 border-gray-400 ring ring-transparent ring-opacity-0',
                'focus:outline-none focus-within:border-gray-300 focus-within:ring-opacity-50 focus-within:ring-indigo-200',
                'transition duration-quick ease-in-out',
            ) }}"
            type="button"
            x-ref="button"
            x-on:click.prevent="toggleListboxVisibility()"
            x-bind:aria-expanded="open"
            aria-haspopup="listbox"
        >
            <div
                class="w-full h-full min-h-6-em truncate text-left"
                x-show="! open"
                x-text="value in options ? options[value] : placeholder"
                x-bind:class="{ 'text-gray-500': !(value in options) }"
            ></div>

            <input
                class="w-full h-full min-h-6-em p-0 border-none bg-transparent focus:outline-none focus:ring-transparent"
                x-ref="search"
                x-show="open"
                x-model="search"
                x-on:keydown.enter.stop.prevent="selectOption()"
                x-on:keydown.arrow-up.prevent="focusPreviousOption()"
                x-on:keydown.arrow-down.prevent="focusNextOption()"
                x-on:keydown.tab="closeListbox()"
                type="search"
            />

            <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                <svg
                    class="w-5 h-5 text-gray-400"
                    viewBox="0 0 20 20"
                    fill="none"
                    stroke="currentColor"
                >
                    <path
                        d="M7 7l3-3 3 3m0 6l-3 3-3-3"
                        stroke-width="1.5"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    ></path>
                </svg>
            </span>

        </button>
    </div>

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
            class="py-1 overflow-auto text-base leading-6 max-h-60 focus:outline-none"
            x-ref="listbox"
            x-on:keydown.enter.stop.prevent="selectOption()"
            x-on:keydown.arrow-up.prevent="focusPreviousOption()"
            x-on:keydown.arrow-down.prevent="focusNextOption()"
            role="listbox"
            x-bind:aria-activedescendant="focusedOptionIndex ? name + 'Option' + focusedOptionIndex : null"
            tabindex="-1"
        >
            <template x-for="(key, index) in Object.keys(options)" :key="index">
                <li
                    class="relative py-2 pl-3 cursor-default select-none pr-9"
                    x-bind:id="name + 'Option' + focusedOptionIndex"
                    x-on:click="selectOption()"
                    x-on:mouseenter="focusedOptionIndex = index"
                    x-on:mouseleave="focusedOptionIndex = null"
                    role="option"
                    x-bind:aria-selected="focusedOptionIndex === index"
                    x-bind:class="{
                        'text-gray-300 bg-navy-500': index === focusedOptionIndex,
                        'text-gray-100': index !== focusedOptionIndex
                    }"
                >
                    <span
                        class="block font-normal truncate"
                        x-text="Object.values(options)[index]"
                        x-bind:class="{
                            'font-semibold': index === focusedOptionIndex,
                            'font-normal': index !== focusedOptionIndex
                        }"
                    ></span>

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

            <div
                class="px-3 py-2 text-gray-900 cursor-default select-none"
                x-show="! Object.keys(options).length"
                x-text="emptyOptionsMessage"
            ></div>
        </ul>
    </div>

</div>
