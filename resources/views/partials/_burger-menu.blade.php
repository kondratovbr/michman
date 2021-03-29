{{--TODO: IMPORTANT! Placeholders. These should be dropdowns with user's servers/projects respectively. Like in Forge.--}}
{{--TODO: CRITICAL! Don't forget to put real routes here!--}}

<div class="h-full w-full flex justify-start">
    <button
        class="group flex flex-col items-stretch py-1 px-2 cursor-pointer select-none focus:outline-none"
        x-on:click="open = !open"
    >
        <div
            class="py-3 px-4 text-gray-200 rounded-md border border-gray-300 border-opacity-0 bg-navy-400 bg-opacity-0 group-hover:border-opacity-100 group-active:bg-opacity-100 group-hover:text-gray-100 group-focus:border-opacity-100 transition-border-background ease-in-out duration-quick"
        >
            <div class="transform group-hover:scale-105 transition-transform ease-in-out duration-quick">
                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path
                        x-bind:class="{ 'hidden': open, 'inline-flex': !open }"
                        stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"
                    />
                    <path
                        x-bind:class="{ 'hidden': !open, 'inline-flex': open }"
                        x-cloak
                        stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"
                    />
                </svg>
            </div>
        </div>
    </button>
</div>

<x-burger.separator/>

<x-burger.link
    href="{{ route('home') }}"
    routeName="home"
>
    <x-slot name="icon"><i class="fa fa-server"></i></x-slot>
    {{ __('nav.servers') }}
</x-burger.link>
<x-burger.link
    href="{{ route('home') }}"
    routeName="home"
>
    <x-slot name="icon"><i class="fa fa-hard-hat"></i></x-slot>
    {{ __('nav.projects') }}
</x-burger.link>
{{--    TODO: Add external link icon. Maybe animate it on hover/active.--}}
<x-burger.link
    href="{{ route('home') }}"
    routeName="home"
>
    <x-slot name="icon"><i class="far fa-file-alt"></i></x-slot>
    {{ __('nav.documentation') }}
</x-burger.link>

<x-burger.separator/>

{{-- Responsive Settings Options --}}
<div class="py-1 px-4 flex items-center space-x-2">
    <x-avatar class="h-10 w-10" />
    <span class="text-sm font-medium">{{ user()->email }}</span>
</div>

{{-- Account Management --}}
<x-burger.link
    href="{{ route('profile.show') }}"
    :active="request()->routeIs('profile.show')"
>
    <x-slot name="icon"><i class="far fa-user"></i></x-slot>
    {{ __('nav.account') }}
</x-burger.link>
<x-burger.link
    href="{{ route('profile.show') }}"
    :active="request()->routeIs('profile.show')"
>
    <x-slot name="icon"><i class="far fa-money-bill-alt"></i></x-slot>
    {{ __('nav.billing') }}
</x-burger.link>
@if (Laravel\Jetstream\Jetstream::hasApiFeatures())
    <x-burger.link
        href="{{ route('api-tokens.index') }}"
        :active="request()->routeIs('api-tokens.index')"
    >
        <x-slot name="icon"><i class="fa fa-ship"></i></x-slot>
        {{ __('nav.api-tokens') }}
    </x-burger.link>
@endif

<x-burger.separator/>

{{-- Authentication --}}
<x-form method="POST" action="{{ route('logout') }}">
    <x-burger.link
        href="{{ route('logout') }}"
        onclick="
            event.preventDefault();
            this.closest('form').submit();
        "
    >
        <x-slot name="icon"><i class="fa fa-sign-out-alt fa-flip-horizontal"></i></x-slot>
        {{ __('auth.logout') }}
    </x-burger.link>
</x-form>

{{-- Team Management --}}
{{--        TODO: Temporarily disabled by this "false &&" thing. Don't forget to implement with the teams feature.--}}
@if (false && Laravel\Jetstream\Jetstream::hasTeamFeatures())
    <x-burger.separator/>

    <div class="block px-4 py-2 text-xs">
        {{ __('Manage Team') }}
    </div>

    {{-- Team Settings --}}
    <x-burger.link
        href="{{ route('teams.show', Auth::user()->currentTeam->id) }}"
        :active="request()->routeIs('teams.show')"
    >{{ __('Team Settings') }}</x-burger.link>

    @can('create', Laravel\Jetstream\Jetstream::newTeamModel())
        <x-burger.link
            href="{{ route('teams.create') }}"
            :active="request()->routeIs('teams.create')"
        >{{ __('Create New Team') }}</x-burger.link>
    @endcan

    <div class="border-t border-gray-200"></div>

    {{-- Team Switcher --}}
    <div class="block px-4 py-2 text-xs">
        {{ __('Switch Teams') }}
    </div>

    @foreach (user()->allTeams() as $team)
        <x-jet-switchable-team :team="$team" component="jet-responsive-nav-link" />
    @endforeach
@endif
