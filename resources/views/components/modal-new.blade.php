{{--TODO: VERY IMPORTANT! Unfinished. Test responsiveness and test on touch - both tablet and mobile.--}}

{{-- Container for a modal --}}
<div
    {{-- Some Livewire functions require a unique ID here. --}}
    id="{{ $id ?? md5($attributes->wire('model')) }}"
    class="fixed inset-0 overflow-y-auto z-50"
{{--        TODO: IMPORTANT! Test the scroll prevention om mobile. --}}
{{--        TODO: If this thing even works - maybe extract it to a componnt.--}}
    {{-- Livewire model (which should be a bool indicating if the modal is opened or closed) will always be the same as Alpines "show" variable used here. Syncronization works both ways. --}}
    x-data="{
        show: @entangle($attributes->wire('model')),
        disableScroll() {
            document.body.classList.add('overflow-y-hidden');
        },
        enableScroll() {
            document.body.classList.remove('overflow-y-hidden');
        },
    }"
    x-init="
        if (show) disableScroll();

        $watch('show', value => {
            if (value) {
                disableScroll();
            } else {
                enableScroll();
            }
        });
    "
    x-show="show"
    x-cloak
    {{-- Close modal on ESC button and similar actions --}}
    x-on:close.stop="show = false"
    x-on:keydown.escape.window="show = false"
    {{-- These allow to throw an event from somewhere inside the modal to close or open it. Useful for "X"/"Close"/"Cancel" buttons.--}}
    x-on:close-modal.prevent="show = false"
    x-on:open-modal.prevent="show = true"
    x-on:close-modal.window.prevent="show = false"
    x-on:open-modal.window.prevent="show = true"
>
    {{-- Opaque background container - needed for proper transitions. --}}
    {{-- Separated from the modal box itself to be able to have different transitions on them. --}}
    <div
        x-show="show"
        x-cloak
        class="fixed inset-0 transition-opacity"
        {{-- Close modal on click on the background --}}
        x-on:click.stop="show = false"
        {{-- Transitions for opening the modal --}}
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        {{-- Transitions for closing the modal --}}
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        {{-- The background itself --}}
        <div class="absolute inset-0 bg-navy-100 opacity-75"></div>
    </div>

    {{-- Container for the modal box --}}
    <div
        x-show="show"
        x-cloak
        class="h-full container mx-auto transform transition-opacity-transform"
        {{-- Close modal on click on the background --}}
        x-on:click.stop="show = false"
        {{-- Transitions for opening the modal --}}
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
        {{-- Transitions for closing the modal --}}
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
    >
        <div
            class="h-full w-full"
{{--            TODO: Maybe I can replace this whole piece of JS with Alpine's "x-trap" plugin. See: https://alpinejs.dev/plugins/trap--}}
            {{-- This component contains functions that handle focus changes (Tab button),
            so that when modal is shown focus is kept inside the modal. --}}
            x-data="focusableDialog()"
{{--            x-init="init()"--}}
            {{-- These directives overrides focus-changing buttons (tab, shift+tab)
            to use previously declared handlers instead of default logic. --}}
            x-on:keydown.tab.prevent="$event.shiftKey || nextFocusable().focus()"
            x-on:keydown.shift.tab.prevent="prevFocusable().focus()"
        >
            {{ $slot }}
        </div>

    </div>

</div>
