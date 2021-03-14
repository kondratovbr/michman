{{--TODO: Add icons.--}}

<x-navbar.dropdown align="right">

    <x-slot name="trigger">
        <div class="flex items-center space-x-2 text-sm">
            <x-avatar class="h-8 w-8" />
            <span>{{ user()->email }}</span>
        </div>
    </x-slot>

    <x-slot name="content">
        {{-- Account Management --}}
        <x-dropdown.header>
            {{ __('Manage Account') }}
        </x-dropdown.header>

        <x-dropdown.link href="{{ route('profile.show') }}">
            {{ __('nav.account') }}
        </x-dropdown.link>

        <x-dropdown.link href="">
            {{ __('nav.billing') }}
        </x-dropdown.link>

        @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
            <x-dropdown.link href="{{ route('api-tokens.index') }}">
                {{ __('API Tokens') }}
            </x-dropdown.link>
        @endif

        <x-dropdown.separator/>

        {{-- Authentication --}}
        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <x-dropdown.link
                href="{{ route('logout') }}"
                onclick="
                    event.preventDefault();
                    this.closest('form').submit();
                "
            >
                {{ __('Log Out') }}
            </x-dropdown.link>
        </form>
    </x-slot>
</x-navbar.dropdown>
