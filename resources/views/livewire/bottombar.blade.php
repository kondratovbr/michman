<nav class="block md:hidden fixed bottom-0 w-screen bg-navy-300 rounded-t-2xl shadow-lg-top z-40">
    <div class="h-16 mx-auto pb-0-safe grid grid-cols-12 divide-x divide-gray-600">

        <x-bottombar.link routeName="home" class="col-span-2">
            <x-logo class="block h-9 w-auto" />
        </x-bottombar.link>

        <x-bottombar.link routeName="home" class="col-span-4">
            <x-slot name="icon"><i class="fa fa-server"></i></x-slot>
            <x-slot name="content">Servers</x-slot>
        </x-bottombar.link>

        <x-bottombar.link routeName="home" class="col-span-4">
            <x-slot name="icon"><i class="fa fa-hard-hat"></i></x-slot>
            <x-slot name="content">Projects</x-slot>
        </x-bottombar.link>

        <x-bottombar.dropup class="col-span-2">
            <x-slot name="trigger">
                <x-icon class="text-2xl" size="8">
                    <i class="fas fa-bars" x-show="!open && sub === ''"></i>
                    <i class="fas fa-times" x-show="open || sub !== ''" x-cloak></i>
                </x-icon>
            </x-slot>

            <x-dropdown.menu drop="up" align="right" minWidth="64">
                @include('bottombar._main-dropup')
            </x-dropdown.menu>

            <x-dropdown.menu drop="up" align="right" minWidth="64" show="sub === 'account'">
                @include('bottombar._account-dropup')
            </x-dropdown.menu>

        </x-bottombar.dropup>

    </div>
</nav>
