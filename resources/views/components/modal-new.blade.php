{{--TODO: IMPORTANT! Unfinished. Responsiveness, mobile. Test on touch - both tablet and mobile.--}}
{{--TODO: Can I make this generic modal to be one single element with various modal boxes inside? I will have to decouple the normal components from the modal parts of it though.--}}

{{-- Container for a modal --}}
<div
    {{-- Some Livewire functions require a unique ID here. --}}
{{--    id="{{ $id ?? md5($attributes->wire('model')) }}"--}}
    class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-40"
    {{-- Livewire model (which should be a bool indicating if the modal is opened or closed) will always be the same as Alpines "show" variable used here. Syncronization works both ways. --}}
    x-data="{ show: @entangle($attributes->wire('model')) }"
    x-show="show"
    {{-- This is needed so the modal doesn't flash during page load (before Alpine kicks in and hides the thing). --}}
    {{-- Tailwind "hidden" doesn't cut it - Alpine has no idea how to handle it. --}}
    style="display: none"
    {{-- Close modal on ESC button and similar actions --}}
    x-on:close.stop="show = false"
    x-on:keydown.escape.window="show = false"
>
    {{-- Opaque background container - needed for proper transitions. --}}
    {{-- Separated from the modal box itself to be able to have different transitions on them. --}}
    <div
        x-show="show"
        style="display: none"
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
        style="display: none"
        class="container mx-auto transform transition-opacity-transform"
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
        {{ $slot }}
    </div>

</div>
