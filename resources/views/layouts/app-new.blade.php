<x-layouts.html>

{{--    TODO: IMPORTANT! WTF is this? Does it even work?--}}
    <x-jet-banner/>

    <div class="min-h-screen">

        <livewire:navbar/>

        {{-- Main Layout Container --}}
        <div class="container mx-auto pb-20 md:grid md:grid-cols-12">
            {{-- Responsiveness Containment --}}
{{--            TODO: IMPORTANT! Unfinished. Figure out responsiveness and mobile.--}}
            <div class="md:col-start-2 md:col-end-12">

                {{ $slot }}

            </div>
        </div>

    </div>

</x-layouts.html>
