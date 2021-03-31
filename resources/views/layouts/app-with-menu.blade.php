<x-layouts.app-new>

    {{-- Page Heading --}}
    @isset($header)
        <header class="py-8">
            {{-- This is needed to align the header with the aside menu buttons --}}
            <div class="ml-4">
                {{ $header }}
            </div>
        </header>
    @endisset

    {{-- Page Content --}}
{{--    TODO: IMPORTANT! Responsiveness and mobile unfinished!--}}
    <div class="md:grid md:grid-cols-12">

        {{-- Side Menu --}}
        <div class="md:col-span-3 pr-5">
            {{ $menu }}
        </div>

        {{-- Main Page Content --}}
        <main class="md:col-span-9">
            {{ $slot }}
        </main>

    </div>

</x-layouts.app-new>
