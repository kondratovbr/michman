<div
    x-show="open"
    class="lg:hidden fixed inset-0 overflow-hidden"
    x-cloak

{{--    TODO: WTF is this doing?--}}
    x-transition:enter=""
    x-transition:enter-start=""
    x-transition:enter-end=""
    x-transition:leave="transition duration-500 md:duration-700"
    x-transition:leave-start=""
    x-transition:leave-end=""
>

    {{-- Opaque background --}}
    <div
        class="md:hidden absolute inset-0"
        x-show="open"
        x-cloak
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div class="absolute inset-0 bg-navy-100 opacity-75"></div>
    </div>

    {{-- Container for the menu itself --}}
    <div
        class="md:hidden absolute right-0 max-w-sm"
        x-show="open"
        x-cloak
        x-on:click.away="open = false"
        x-on:close.stop="open = false"

        x-transition:enter="transform transition ease-out duration-300 md:duration-500"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transform transition ease-in duration-150 md:duration-300"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
    >
        {{-- Menu content --}}
        <div class="py-2 bg-navy-300 rounded-bl-md border-b border-l border-gray-600 shadow-lg">

            {{ $slot }}

        </div>
    </div>

</div>
