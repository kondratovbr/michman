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
