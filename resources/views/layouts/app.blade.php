<x-layouts.html>

    <x-jet-banner/>

    <div class="min-h-screen">
        <livewire:navbar/>

        {{-- Page Heading --}}
        @isset($header)
            <header>
                <div class="container mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        {{-- Page Content --}}
        <main>
            {{ $slot }}
        </main>

    </div>

    @stack('modals')

</x-layouts.html>
