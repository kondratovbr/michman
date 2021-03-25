{{--TODO: Add icons everywhere.--}}

<nav
    class="relative bg-navy-300 z-40"
    x-data="{ open: true }"
>

    {{-- Primary Navigation Menu --}}
    <div class="h-16 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between">

        {{-- Left Side --}}
        <div class="flex items-stretch">

            {{-- Logo --}}
            <div class="flex-shrink-0 flex">
                <x-navbar.link routeName="home">
                    <x-logo class="block h-9 w-auto" />
                </x-navbar.link>
            </div>

            {{-- Navigation Links --}}
            <div class="hidden sm:flex sm:-my-px sm:ml-10">
{{--                TODO: IMPORTANT! Placeholders. These should be dropdowns with user's servers/projects respectively. Like in Forge.--}}
                <x-navbar.link routeName="home">
                    <x-slot name="icon"><i class="fa fa-server"></i></x-slot>
                    {{ __('nav.servers') }}
                </x-navbar.link>
                <x-navbar.link routeName="home">
                    <x-slot name="icon"><i class="fa fa-hard-hat"></i></x-slot>
                    {{ __('nav.projects') }}
                </x-navbar.link>
{{--                TODO: Add external link icon. Maybe animate it on hover/active.--}}
                <x-navbar.link routeName="home">
                    <x-slot name="icon"><i class="far fa-file-alt"></i></x-slot>
                    {{ __('nav.documentation') }}
                </x-navbar.link>
            </div>

        </div>

        {{-- Right Side --}}
        <div class="hidden sm:flex sm:items-stretch">
            {{-- Teams Dropdown --}}
{{--            @if (Laravel\Jetstream\Jetstream::hasTeamFeatures())--}}
{{--                @include('partials._teams-dropdown')--}}
{{--            @endif--}}

            {{-- Account Dropdown --}}
            @include('partials._account-dropdown')
        </div>

        {{-- Burger Button --}}
        <div class="-mr-2 flex items-center sm:hidden">
            @include('partials._burger-button')
        </div>

    </div>



    {{-- Mobile Burger Menu --}}
    <div
        x-show="open"
        class="sm:hidden fixed inset-0 overflow-hidden"
        x-cloak

        x-transition:enter=""
        x-transition:enter-start=""
        x-transition:enter-end=""
        x-transition:leave="transition duration-500 sm:duration-700"
        x-transition:leave-start=""
        x-transition:leave-end=""
    >

        {{-- Opaque background --}}
        <div
            class="sm:hidden absolute inset-0"
            x-show="open"
            x-cloak
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
        >
            <div class="absolute inset-0 bg-navy-100 opacity-75"></div>
        </div>

        {{-- Container for the menu itself --}}
        <div
            class="sm:hidden absolute right-0 max-w-sm"
            x-show="open"
            x-cloak
            x-on:click.away="open = false"
            x-on:close.stop="open = false"

            x-transition:enter="transform transition ease-out duration-300 sm:duration-500"
            x-transition:enter-start="translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transform transition ease-in duration-150 sm:duration-300"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
        >
            {{-- Menu content --}}
            <div class="bg-navy-300">
                @include('partials._burger-menu')
            </div>
        </div>

    </div>



</nav>
