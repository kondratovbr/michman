<x-layouts.html>

{{--    TODO: IMPORTANT! WTF is this? Does it even work?--}}
    <x-jet-banner/>

    <div class="min-h-screen">

{{--        TODO: Maybe turn the navbar and bottombar into a Livewire component and don't reload the whole page on navigation at all?--}}

        <livewire:navbar/>

        {{-- Bottombar has "fixed" positioning to the bottom.
        It is placed here so it loads before the rest of the page. --}}
        <livewire:bottombar/>

        {{-- Main Layout Container --}}
        <div class="container mx-auto pb-20 md:grid md:grid-cols-12">
            {{-- Responsiveness Containment --}}
{{--            TODO: IMPORTANT! Unfinished. Figure out responsiveness and mobile.--}}
            <div class="md:col-span-12 xl:col-start-2 xl:col-end-12">

                {{ $slot }}

            </div>
        </div>

    </div>

</x-layouts.html>
