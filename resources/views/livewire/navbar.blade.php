{{--TODO: Add icons.--}}

<nav x-data="{ open: false }" class="bg-navy-300">

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
{{--                    TODO: IMPORTANT! Placeholders. These should be dropdowns with user's servers/projects respectively. Like in Forge.--}}
                <x-navbar.link routeName="home">
                    <x-slot name="icon"><i class="far fa-server"></i></x-slot>
                    {{ __('nav.servers') }}
                </x-navbar.link>
                <x-navbar.link routeName="home">
                    <x-slot name="icon"><i class="far fa-hard-hat"></i></x-slot>
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
    <div x-bind:class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        @include('partials._burger-menu')
    </div>
</nav>
