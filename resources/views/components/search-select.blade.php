{{--TODO: Does it need something changed for smaller screens?--}}
{{--TODO: IMPORTANT! Make sure it us usable on touch.--}}
{{--TODO: Google some a11y guide for such menus and make sure we're alright.
          Also, check out:
          https://www.w3.org/TR/wai-aria-practices/#Listbox
          https://www.w3.org/TR/wai-aria-practices/examples/listbox/listbox-collapsible.html--}}

@props(['data', 'name', 'placeholder' => ' ', 'emptyOptionsMessage' => ' '])

<div
    x-data="searchSelect({ @alpine($name, $data, $placeholder, $emptyOptionsMessage) })"
    x-init="init()"
    x-on:click.away="closeListbox(false)"
    x-on:keydown.escape="closeListbox()"
    class="relative"
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
        {{-- TODO: This is recommended to have for a11y. Should point to an ID of the label for this thing. --}}
{{--            aria-labelledby="listbox-label"--}}
        aria-haspopup="listbox"
    >
        {{-- Name of a currently chosen option or a placeholder --}}
        <div
            class="w-full h-full min-h-6-em truncate text-left select-none"
            x-show="! open"
            x-text="value in options ? options[value] : placeholder"
            x-bind:class="{ 'text-gray-500': !(value in options) }"
        ></div>

        {{-- Input box for searching --}}
        <input
            class="w-full h-full min-h-6-em p-0 border-none bg-transparent focus:outline-none focus:ring-transparent"
            x-ref="search"
            x-show="open"
            x-model="search"
            x-on:keydown.enter.stop.prevent="selectOption()"
            x-on:keydown.arrow-up.prevent="focusPreviousOption()"
            x-on:keydown.arrow-down.prevent="focusNextOption()"
            x-on:keydown.tab="closeListbox()"
        />

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
            class="py-1 w-full max-h-60 overflow-auto text-base leading-6 focus:outline-none"
            x-ref="listbox"
            x-on:keydown.enter.stop.prevent="selectOption()"
            x-on:keydown.arrow-up.prevent="focusPreviousOption()"
            x-on:keydown.arrow-down.prevent="focusNextOption()"
            role="listbox"
            {{-- TODO: This is recommended to have for a11y. Should point to an ID of the label for this thing. --}}
{{--            aria-labelledby="listbox-label"--}}
            x-bind:aria-activedescendant="focusedOptionIndex ? name + 'Option' + focusedOptionIndex : null"
            tabindex="-1"
        >
            <template x-for="(key, index) in Object.keys(options)" :key="index">
                <li
                    class="relative py-2 pl-3 pr-9 cursor-pointer select-none"
                    x-bind:id="name + 'Option' + focusedOptionIndex"
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

            {{-- A message shown if no results found for a given search string --}}
            <div
                class="px-3 py-2 text-gray-300 cursor-default select-none"
                x-show="! Object.keys(options).length"
                x-text="emptyOptionsMessage"
            ></div>
        </ul>
    </div>

</div>
