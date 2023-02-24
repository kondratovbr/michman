<x-layouts.app-new>

    {{-- Main Container - to make the single column layout narrower --}}
    <div class="lg:grid lg:grid-cols-24">
        <div class="lg:col-start-4 lg:col-end-[22] xl:col-start-4 xl:col-end-[22]">

            @isset($notifications)
                <div class="mt-8">{{ $notifications }}</div>
            @endisset

            {{-- A place to put a section that should be positioned above the header. --}}
            <section>
                @isset($above)
                    {{ $above }}
                @endisset
            </section>

            {{-- Page Heading --}}
            <header class="py-8 pl-4 sm:pl-0">
                {{-- Keeping an empty header for padding if none provided. --}}
                @isset($header)
                    {{ $header }}
                @endisset
            </header>

            {{-- Page Content --}}
            <main>
                {{ $slot }}
            </main>

        </div>
    </div>

</x-layouts.app-new>
