{{--TODO: Does it need something changed for smaller screens?--}}
{{--TODO: IMPORTANT! Make sure it us usable on touch.--}}
{{--TODO: Google some a11y guide for such menus and make sure we're alright.
          Also, check out:
          https://www.w3.org/TR/wai-aria-practices/#Listbox
          https://www.w3.org/TR/wai-aria-practices/examples/listbox/listbox-collapsible.html--}}

{{--TODO: IMPORTANT! Add an error state styling.--}}

@props([
    'name',
    'id',
    'options' => [],
    'default' => false,
    'placeholder' => ' ',
    'labelId',
    'showErrors' => true,
    'errorName'
])

<div
    class="relative"
    x-data="searchSelect({
        @if($attributes->wire('model')->value())
            wireModel: '{{ $attributes->wire('model')->value() }}',
        @endif
    })"
    @if(! $default)
        x-init="$refs.select.selectedIndex = -1;"
    @endif
    x-on:click.outside="closeListbox()"
    x-on:keydown.escape="closeListbox(true)"
>
    {{-- Hidden select for keeping the state and interacting with Livewire --}}
    <select
        class="hidden"
        name="{{ $name }}"
        id="{{ $id ?? $name }}"
        {{ $attributes->wire('model') }}
        x-ref="select"
    >
        @foreach($options as $value => $valueString)
            <option value="{{ $value }}">{{ $valueString }}</option>
        @endforeach
    </select>

    {{-- Activation button --}}
    <button
        class="{{ classes(
            'relative w-full py-2 pl-3 pr-10 rounded-md cursor-pointer select-none',
            'bg-navy-300 border-2 border-gray-400 ring ring-transparent ring-opacity-0',
            'focus:outline-none focus:ring-opacity-50 focus-within:outline-none focus-within:ring-opacity-50',
            ($showErrors && $errors->has($errorName ?? $name))
                ? 'border-red-600 hover:border-red-500 focus:border-red-500 focus:ring-red-300 focus-within:border-red-500 focus-within:ring-red-300'
                : 'border-gray-400 hover:border-gray-300 focus:border-gray-300 focus:ring-indigo-200 focus-within:border-gray-300 focus-within:ring-indigo-200',
            'transition duration-quick ease-in-out',
        ) }}"
        type="button"
        x-ref="button"
        x-on:click.prevent="toggleListbox()"
        x-on:keydown.arrow-up.stop.prevent="focusPreviousOption()"
        x-on:keydown.arrow-down.stop.prevent="focusNextOption()"
        x-on:keydown.enter.stop.prevent="selectOption(true)"
        x-on:keydown.tab="closeListbox()"
        aria-haspopup="listbox"
        x-bind:aria-expanded="open"
        @isset($labelId)
            aria-labelledby="{{ $labelId }}"
        @endisset
    >
        {{-- Name of a currently chosen option or a placeholder --}}
        <div
            class="w-full h-full min-h-6-em truncate text-left pointer-events-none select-none"
            x-show="! open"
            x-text="
                $refs.select.selectedIndex >= 0
                    ? $refs.select.options[$refs.select.selectedIndex].text
                    : '{{ $placeholder }}'
            "
            x-bind:class="{ 'text-gray-500': $refs.select.selectedIndex < 0 }"
        ></div>

        {{-- Input box for filtering the options --}}
        <input
            class="w-full h-full min-h-6-em p-0 border-none bg-transparent focus:outline-none focus:ring-transparent"
            x-ref="search"
            x-show="open"
            x-model="search"
            x-on:keydown.enter.stop.prevent="selectOption(true)"
            x-on:keydown.arrow-up.stop.prevent="focusPreviousOption()"
            x-on:keydown.arrow-down.stop.prevent="focusNextOption()"
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

    {{-- Dropdown select menu container --}}
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
        {{-- The select menu itself --}}
        <ul
            class="py-1 w-full max-h-64 overflow-auto text-base leading-6 max-h-60 focus:outline-none"
            x-ref="listbox"
            x-on:keydown.enter.stop.prevent="selectOption(true)"
            x-on:keydown.arrow-up.stop.prevent="focusPreviousOption()"
            x-on:keydown.arrow-down.stop.prevent="focusNextOption()"
            tabindex="-1"
            role="listbox"
            @isset($labelId)
                aria-labelledby="{{ $labelId }}"
            @endisset
            x-bind:aria-activedescendant="'{{ $name }}' + '-option-' + focusedOptionIndex"
        >
            <template x-for="index in $refs.select.options.length" :key="index - 1">
                <li
                    class="{{ classes(
                        'relative py-2 pl-3 pr-9 cursor-pointer select-none',
                    ) }}"
                    x-show="optionShown(index - 1)"
                    x-bind:id="'{{ $name }}' + '-option-' + (index - 1)"
                    x-on:mouseenter="focusIndex(index - 1)"
                    x-on:mouseleave="focusIndex(null)"
                    x-on:click="selectOption()"
                    x-bind:class="{
                        'text-gray-100 bg-navy-500': focusedOptionIndex === index - 1,
                        'text-gray-200': focusedOptionIndex !== index - 1,
                    }"
                    role="option"
                    x-bind:aria-selected="focusedOptionIndex === index - 1"
                >
                    {{-- Name of an option --}}
                    <span
                        class="block truncate"
                        x-text="$refs.select.options[index - 1].text"
                    ></span>

                    {{-- Checkmark icon for a selected option --}}
                    <span
                        class="absolute inset-y-0 right-0 items-center pr-4"
                        x-bind:class="{
                            'flex': index - 1 === $refs.select.selectedIndex,
                            'hidden': index - 1 !== $refs.select.selectedIndex,
                        }"
                    ><x-icon><i class="fas fa-check"></i></x-icon></span>
                </li>
            </template>
        </ul>
    </div>

</div>
