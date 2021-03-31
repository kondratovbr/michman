{{--TODO: Add icons everywhere.--}}

<nav
    class="relative hidden md:block bg-navy-300 z-40"
    x-data="{ open: false }"
>

    {{-- Primary Navigation Menu --}}
    <div class="h-16 max-w-7xl mx-auto px-4 md:px-6 lg:px-8 flex justify-between">

        {{-- Left Side --}}
        <div class="flex items-stretch">

            {{-- Logo --}}
            <div class="flex-shrink-0 flex">
                <x-navbar.link routeName="home">
                    <x-logo class="block h-9 w-auto" />
                </x-navbar.link>
            </div>

            {{-- Navigation Links --}}
            <div class="hidden md:flex md:-my-px md:ml-10">
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
        <div class="hidden md:flex md:items-stretch">
            {{-- Teams Dropdown --}}
{{--            @if (Laravel\Jetstream\Jetstream::hasTeamFeatures())--}}
{{--                @include('partials._teams-dropdown')--}}
{{--            @endif--}}

            {{-- Account Dropdown --}}
            @include('partials._account-dropdown')

        </div>

        <div class="-mr-2 flex items-center md:hidden">
        </div>

    </div>

</nav>
