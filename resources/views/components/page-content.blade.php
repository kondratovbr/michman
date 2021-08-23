<div class="md:grid md:grid-cols-12">

    {{-- Side Menu --}}
    <div class="hidden md:block md:col-span-3 md:pr-5">
        {{ $menu }}
    </div>

    <div class="md:col-span-9">
        {{-- Notifications --}}
        @isset($notifications)
            {{ $notifications }}
        @endisset

        {{-- Main Page Content --}}
        <main>
            {{ $slot }}
        </main>
    </div>

</div>
