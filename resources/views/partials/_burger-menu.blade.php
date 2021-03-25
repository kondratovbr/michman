<div class="pt-2 pb-3 space-y-1">
{{--    TODO: IMPORTANT! Placeholders. These should be dropdowns with user's servers/projects respectively. Like in Forge.--}}
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
</div>

{{-- Responsive Settings Options --}}
<div class="pt-4 pb-1 border-t border-gray-200">
    <div class="flex items-center px-4">
        @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
            <div class="flex-shrink-0 mr-3">
                <img
                    class="h-10 w-10 rounded-full object-cover"
                    src="{{ user()->avatarUrl }}"
                    alt="{{ user()->name }}"
                />
            </div>
        @endif

        <div>
            <div class="font-medium text-base">{{ user()->name }}</div>
            <div class="font-medium text-sm">{{ user()->email }}</div>
        </div>
    </div>

    <div class="mt-3 space-y-1">
        {{-- Account Management --}}
        <x-burger.link
            href="{{ route('profile.show') }}"
            :active="request()->routeIs('profile.show')"
        >
            {{ __('Profile') }}
        </x-burger.link>

        @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
            <x-burger.link
                href="{{ route('api-tokens.index') }}"
                :active="request()->routeIs('api-tokens.index')"
            >
                {{ __('API Tokens') }}
            </x-burger.link>
        @endif

        {{-- Authentication --}}
        <x-form method="POST" action="{{ route('logout') }}">
            <x-burger.link
                href="{{ route('logout') }}"
                onclick="
                    event.preventDefault();
                    this.closest('form').submit();
                "
            >{{ __('auth.logout') }}</x-burger.link>
        </x-form>

        {{-- Team Management --}}
        @if (Laravel\Jetstream\Jetstream::hasTeamFeatures())
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

    </div>
</div>
