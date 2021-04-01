<x-layouts.app-new>

    {{-- Page Heading --}}
    @isset($header)
        <header class="py-8">
            {{-- Margin on <sm aligns the page header with section titles.
                 Margin on md> aligns the page header with the side menu buttons. --}}
            <div class="ml-4 sm:ml-0 md:ml-4">
                {{ $header }}
            </div>
        </header>
    @endisset

    {{-- Page Content --}}
{{--    TODO: IMPORTANT! Responsiveness and mobile unfinished!--}}
    <div class="md:grid md:grid-cols-12">

        {{-- Side Menu --}}
        <div class="hidden md:block md:col-span-3 md:pr-5">
            {{ $menu }}
        </div>

        {{-- Main Page Content --}}
        <main class="md:col-span-9">
            {{ $slot }}
        </main>

    </div>

</x-layouts.app-new>
