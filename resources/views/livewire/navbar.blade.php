{{--TODO: Add icons.--}}

<nav x-data="{ open: false }" class="bg-navy-300">

    {{-- Primary Navigation Menu --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">

            <div class="flex items-stretch">

                {{-- Logo --}}
                <div class="flex-shrink-0 flex items-center">
                    <a href="{{ route('home') }}">
                        <x-logo class="block h-9 w-auto" />
                    </a>
                </div>

                {{-- Navigation Links --}}
                <div class="hidden sm:flex space-x-8 sm:-my-px sm:ml-10">
                    <x-navbar.link routeName="dashboard">
                        {{ __('Dashboard') }}
                    </x-navbar.link>
{{--                    TODO: IMPORTANT! Remove this. This is temporary.--}}
                    <x-navbar.link routeName="profile.show">
                        Profile Page
                    </x-navbar.link>
{{--                    TODO: IMPORTANT! Placeholders. These should be dropdowns with user's servers/projects respectively. Like in Forge.--}}
                    <x-navbar.link routeName="home">
                        {{ __('nav.servers') }}
                    </x-navbar.link>
                    <x-navbar.link routeName="home">
                        {{ __('nav.projects') }}
                    </x-navbar.link>
                </div>

            </div>

            <div class="hidden sm:flex sm:items-stretch space-x-3">
                {{-- Teams Dropdown --}}
                @if (Laravel\Jetstream\Jetstream::hasTeamFeatures())
{{--                    <div class="ml-3 relative">--}}
                        @include('partials._teams-dropdown')
{{--                    </div>--}}
                @endif

                {{-- Account Dropdown --}}
{{--                <div class="ml-3 relative">--}}
                    @include('partials._account-dropdown')
{{--                </div>--}}
            </div>

            {{-- Burger Button --}}
            <div class="-mr-2 flex items-center sm:hidden">
                @include('partials._burger-button')
            </div>

        </div>
    </div>

    {{-- Mobile Burger Menu --}}
    <div x-bind:class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        @include('partials._burger-menu')
    </div>
</nav>
