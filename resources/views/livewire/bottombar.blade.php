<nav
    class="block md:hidden fixed bottom-0 w-screen bg-navy-300 rounded-t-2xl shadow-xl-top z-40"
    x-data="{ open: true }"
>
    <div class="h-16 mx-auto pb-0-safe grid grid-cols-12 divide-x divide-gray-600">
        <x-bottombar.link routeName="home" class="col-span-2">
            <x-logo class="block h-9 w-auto" />
        </x-bottombar.link>
        <x-bottombar.link routeName="home" class="col-span-4">
            <x-slot name="icon"><i class="fa fa-server"></i></x-slot>
            Servers
        </x-bottombar.link>
        <x-bottombar.link routeName="home" class="col-span-4">
            <x-slot name="icon"><i class="fa fa-hard-hat"></i></x-slot>
            Projects
        </x-bottombar.link>

        {{-- Burger button --}}
        <x-bottombar.link
            class="col-span-2"
            x-on:click="open = !open"
            role="button"
        >
            <x-icon class="text-2xl" size="8">
                <i
                    class="fas fa-bars"
                    x-show="!open"
                ></i>
                <i
                    class="fas fa-times"
                    x-show="open"
                    x-cloak
                ></i>
            </x-icon>
        </x-bottombar.link>

    </div>

</nav>
