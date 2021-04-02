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

    {{ $slot }}

</x-layouts.app-new>
