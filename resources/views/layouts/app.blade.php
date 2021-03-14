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
            <div class="md:col-start-2 md:col-end-12 md:grid md:grid-cols-12">
                @isset($aside)
                    <div class="md:col-span-3 px-5">
                        {{ $aside }}
                    </div>
                @endisset
                {{-- Page Content --}}
                <main class="md:col-span-9 max-w-6xl sm:px-6 lg:px-8">
                    {{ $slot }}
                </main>
            </div>

        </div>

    </div>

    @stack('modals')

</x-layouts.html>
