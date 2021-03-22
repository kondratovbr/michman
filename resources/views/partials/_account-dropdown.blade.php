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
            {{ __('nav.manage_account') }}
        </x-dropdown.header>

        <x-dropdown.link href="{{ route('profile.show') }}">
            <x-slot name="icon"><i class="far fa-user"></i></x-slot>
            {{ __('nav.account') }}
        </x-dropdown.link>

        <x-dropdown.link href="">
            <x-slot name="icon"><i class="far fa-money-bill-alt"></i></x-slot>
            {{ __('nav.billing') }}
        </x-dropdown.link>

        @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
            <x-dropdown.link href="{{ route('api-tokens.index') }}">
                <x-slot name="icon"><i class="far fa-ship"></i></x-slot>
                {{ __('nav.api_token') }}
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
                <x-slot name="icon"><i class="far fa-sign-out-alt fa-flip-horizontal"></i></x-slot>
                {{ __('auth.logout') }}
            </x-dropdown.link>
        </form>
    </x-slot>
</x-navbar.dropdown>
