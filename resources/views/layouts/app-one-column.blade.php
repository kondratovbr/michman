<x-layouts.app-new>

    {{-- Main Container - to make the single column layout narrower --}}
{{--    TODO: IMPORTANT! Responsiveness and mobile unfinished!--}}
    <div class="md:grid md:grid-cols-12">
        <div class="md:col-start-3 md:col-end-11">

            @isset($notifications)
                <div class="mt-8">{{ $notifications }}</div>
            @endisset

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
