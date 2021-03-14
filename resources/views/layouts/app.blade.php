<x-layouts.html>

    <x-jet-banner/>

    <div class="min-h-screen">
        <livewire:navbar/>

        {{-- Main Content Container --}}
        <div class="container mx-auto pb-20 md:grid md:grid-cols-12">

            {{-- Page Heading --}}
            @isset($header)
                <header class="md:col-start-2 md:col-end-12">
                    <div class="py-8 px-4 sm:px-6 lg:px-8">
                        <div class="ml-1">
                            {{ $header }}
                        </div>
                    </div>
                </header>
            @endisset

            {{-- Page Content --}}
            <main class="md:col-start-2 md:col-end-12">
                {{ $slot }}
            </main>

        </div>

    </div>

    @stack('modals')

</x-layouts.html>
