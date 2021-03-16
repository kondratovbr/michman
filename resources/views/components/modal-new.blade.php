{{--TODO: IMPORTANT! Unfinished. Responsiveness, mobile. Test on touch - both tablet and mobile.--}}

{{-- Container for a modal --}}
<div
    class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-40"
    x-data="{ show: @entangle($attributes->wire('model')) }"
    x-show="show"
    {{-- Close modal on ESC button and similar actions --}}
    x-on:close.stop="show = false"
    x-on:keydown.escape.window="show = false"
>
    {{-- Opaque background container - needed for proper transitions. --}}
    <div
        x-show="show"
        class="fixed inset-0 transition-opacity"
        {{-- Close modal on click on the background --}}
        x-on:click="show = false"
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
        class="container mx-auto transform transition-opacity-transform"
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
