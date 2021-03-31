{{--TODO: Add icons everywhere.--}}
{{-- TODO: Don't forget to properly align the actual logo with other elements once it's done. --}}

<nav
    class="relative hidden md:block bg-navy-300 z-40"
    x-data="{ open: false }"
>

    {{-- From sm to lg the entire navbar is the same width as the page content. On xl and 2xl the main part of the navbar is aligned with the content. --}}

    {{-- The content of the navbar --}}
    <div class="{{ classes(
        'h-16',
        'mx-auto max-w-full sm:max-w-screen-sm md:max-w-screen-md lg:max-w-screen-lg xl:max-w-full',
        'flex justify-center items-stretch',
    )}}">

        {{-- Logo container --}}
        {{-- NOTE: It should have the same fixed width as the burger menu button container - to ensure that the middle part is centered. --}}
        <div class="w-24 flex justify-end items-stretch">
            <x-navbar.link routeName="home">
                <x-logo class="block h-9 w-auto" />
            </x-navbar.link>
        </div>

        {{-- Main menu container --}}
        <div class="{{ classes(
            'flex-grow max-w-full xl:max-w-screen-xl-10/12 2xl:max-w-screen-2xl-10/12',
            'flex justify-between items-stretch',
        ) }}">

            {{-- Left side - main navigation --}}
            {{-- NOTE: Negative margin here aligns the first element with the page header and side menu. --}}
            {{-- TODO: This alignment will probably break with one-column layout. Should do something about it. Remember that the navbar is intended to maybe not be reloading during navigation. --}}
            <div class="-ml-1.5 flex items-stretch">
                {{-- Navigation Links --}}
                <div class="hidden md:flex">
{{--                    TODO: IMPORTANT! Placeholders. These should be dropdowns with user's servers/projects respectively. Like in Forge.--}}
                    <x-navbar.link routeName="home">
                        <x-slot name="icon"><i class="fa fa-server"></i></x-slot>
                        {{ __('nav.servers') }}
                    </x-navbar.link>
                    <x-navbar.link routeName="home">
                        <x-slot name="icon"><i class="fa fa-hard-hat"></i></x-slot>
                        {{ __('nav.projects') }}
                    </x-navbar.link>
{{--                    TODO: Add external link icon. Maybe animate it on hover/active.--}}
                    <x-navbar.link routeName="home" class="md:hidden lg:inline-flex">
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
        </div>

        {{-- Burger button container --}}
        {{-- While the menu button is hidden on bigger screens the container servers as a filler to help centering the main content.
             It should always be the same fixed width as the logo container. --}}
        <div class="w-24 hidden md:flex lg:hidden xl:flex justify-end items-stretch">
            <x-navbar.dropdown class="lg:hidden" :chevron="false">
                <x-slot name="trigger">
                    <x-icon class="text-xl" size="6">
                        <i class="fas fa-bars" x-show="!open"></i>
                        <i class="fas fa-times" x-show="open" x-cloak></i>
                    </x-icon>
                </x-slot>

                @include('navbar._menu')

            </x-navbar.dropdown>
        </div>

    </div>

</nav>
