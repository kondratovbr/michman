@props(['name', 'id', 'options' => []])

<div
    class="relative"
    x-data="{
        open: false,
        focusedOptionIndex: null,
        focusNextOption: function () {
            if (this.focusedOptionIndex === null)
                return this.focusedOptionIndex = 0;
            if (this.focusedOptionIndex < this.$refs.select.length - 1)
                return this.focusedOptionIndex++;
        },
        focusPreviousOption: function () {
            if (this.focusedOptionIndex === null)
                return this.focusedOptionIndex = this.$refs.select.length - 1;
            if (this.focusedOptionIndex > 0)
                return this.focusedOptionIndex--;
        },
        selectOption: function () {
            this.$refs.select.selectedIndex = this.focusedOptionIndex;
            {{-- Livewire doesn't get the update event if the value was changed from JS,
                     so we have to manually send the value to its backend.--}}
            @if($attributes->wire('model')->value())
                this.$wire.set('{{ $attributes->wire('model')->value() }}', this.$refs.select.value);
            @endif
            this.open = false;
            this.focusedOptionIndex = null;
        },
}"
    x-init="$refs.select.selectedIndex = -1"
    x-on:click.away="open = false"
    x-on:keydown.escape="open = false; $refs.button.focus()"
>

    <x-buttons.secondary
        x-on:click.prevent="console.log($refs.select.options)"
    >Help me!</x-buttons.secondary>

    <select
{{--        class="hidden"--}}
        name="{{ $name }}"
        id="{{ $id ?? $name }}"
        {{ $attributes->wire('model') }}
        x-ref="select"
    >
        @foreach($options as $value => $valueString)
            <option value="{{ $value }}">{{ $valueString }}</option>
        @endforeach
    </select>

    <button
        class="{{ classes(
            'relative w-full py-2 pl-3 pr-10 rounded-md cursor-pointer select-none',
            'bg-navy-300 border-2 border-gray-400 ring ring-transparent ring-opacity-0',
            'focus:outline-none focus-within:border-gray-300 focus-within:ring-opacity-50 focus-within:ring-indigo-200',
            'transition duration-quick ease-in-out',
        ) }}"
        type="button"
        x-ref="button"
        x-on:click.prevent="
            if (! open) {
                open = true
                focusedOptionIndex = $refs.select.selectedIndex
                if (focusedOptionIndex < 0)
                    focusedOptionIndex = 0
            } else {
                open = false
                focusedOptionIndex = null
            }
        "
        x-on:keydown.arrow-up.prevent="focusPreviousOption()"
        x-on:keydown.arrow-down.prevent="focusNextOption()"
        x-on:keydown.enter.stop.prevent="selectOption()"
    >
        <div
            class="w-full h-full min-h-6-em truncate text-left pointer-events-none select-none"
            x-text="$refs.select.selectedIndex >= 0 ? $refs.select.options[$refs.select.selectedIndex].text : 'Placeholder!'"
        ></div>
    </button>

    <div
        class="absolute z-10 w-full mt-2 bg-navy-400 border border-gray-600 rounded-md shadow-lg origin-top"
        x-show="open"
        x-cloak
    >
        <ul
            class="py-1 w-full max-h-60 overflow-auto text-base leading-6 max-h-60 focus:outline-none"
            x-ref="listbox"
            x-on:keydown.enter.stop.prevent="selectOption()"
            x-on:keydown.arrow-up.prevent="focusPreviousOption()"
            x-on:keydown.arrow-down.prevent="focusNextOption()"
            role="listbox"
        >
            <template x-for="index in $refs.select.options.length" :key="index - 1">
                <li
                    class="{{ classes(
                        'relative py-2 pl-3 pr-9 cursor-pointer select-none',
                        'text-gray-100',
                    ) }}"
                    x-bind:id="'{{ $name }}' + '-option-' + (index - 1)"
                    x-on:mouseenter="focusedOptionIndex = index - 1"
                    x-on:mouseleave="focusedOptionIndex = null"
                    x-on:click="selectOption()"
                    x-bind:class="{
                        'text-gray-300 bg-navy-500': index - 1 === focusedOptionIndex,
                        'text-gray-100': index - 1 !== focusedOptionIndex
                    }"
                    role="option"
                    tabindex="-1"
                >
                    <span
                        class="block truncate"
                        x-text="$refs.select.options[index - 1].text"
                    ></span>
                    <span></span>
                </li>
            </template>
        </ul>
    </div>

</div>
