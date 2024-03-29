{{--TODO: VERY IMPORTANT! This menu item can't handle the browser's "Back" button - the new item doesn't get highlighted after the "Back" button is pressed - try it. Should fix it.--}}

@props(['show', 'disabled' => false])

<li
    {{-- This compensates for the padding on the first and last elements. --}}
    class="first:-mt-1 last:-mb-1"
>
    <button
        class="group w-full h-full inline-flex flex-col items-stretch py-1 cursor-pointer select-none focus:outline-none disabled:cursor-default disabled:opacity-50"
        @isset($show)
            x-on:click="current = '{{ $show }}'"
            wire:click.prefetch="show('{{ $show }}')"
        @endisset
        @if($this->disabled || $disabled || ! $this->canShow($show))
            disabled
        @endif
    >
        <div
            class="capitalize flex items-center py-3 px-4 rounded-lg border border-gray-300 border-opacity-0 bg-navy-300 bg-opacity-0 group-focus:border-opacity-100 transition-border-background ease-in-out duration-quick"
            @unless($this->disabled || $disabled || ! $this->canShow($show))
                x-bind:class="{
                    {{-- Default state --}}
                    'bg-opacity-0 group-hover:border-opacity-100 group-active:bg-opacity-100': current !== '{{ $show }}',
                    {{-- Active (page shown) state --}}
                    'bg-opacity-100': current === '{{ $show }}',
                }"
            @endunless
        >
            <div
                class="md:text-sm lg:text-base transform transition-transform ease-in-out duration-quick"
                @unless($this->disabled || $disabled || ! $this->canShow($show))
                    x-bind:class="{
                        {{-- Default state --}}
                        'group-hover:text-gray-100 group-hover:scale-105': current !== '{{ $show }}',
                        {{-- Active (page shown) state --}}
                        '': current === '{{ $show }}',
                    }"
                @endunless
            >
                @isset($icon)
                    <x-icon class="mr-2">{{ $icon }}</x-icon>
                @endisset
                <span>{{ $slot }}</span>
            </div>
        </div>
    </button>
</li>
