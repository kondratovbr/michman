<x-layouts.html>

{{--    TODO: CRITICAL! WTF is this? Does it even work?--}}
    <x-jet-banner/>

    <div class="min-h-screen">

        <livewire:navbar/>

        {{-- Bottombar has "fixed" positioning to the bottom.
        It is placed here so it loads before the rest of the page. --}}
        <livewire:bottombar/>

        {{-- Main layout container --}}
        <div class="container mx-auto pb-20 md:grid md:grid-cols-12 md:px-2 lg:px-0">
            {{-- Responsiveness containment --}}
            <div class="md:col-span-12 xl:col-start-2 xl:col-end-12">

                {{ $slot }}

            </div>
        </div>

    </div>

</x-layouts.html>
