{{--TODO: Add icons.--}}

<x-jet-dropdown align="right" width="48">

    <x-slot name="trigger">
        <button class="inline-flex items-center space-x-2 text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition duration-150 ease-in-out">
            <x-avatar class="h-8 w-8" />
            <span>{{ user()->email }}</span>
            <x-icon class="fa fa-chevron-down" />
        </button>
    </x-slot>

    <x-slot name="content">
        {{-- Account Management --}}
        <div class="block px-4 py-2 text-xs text-gray-400">
            {{ __('Manage Account') }}
        </div>

        <x-jet-dropdown-link href="{{ route('profile.show') }}">
            {{ __('nav.account') }}
        </x-jet-dropdown-link>

        <x-jet-dropdown-link href="">
            {{ __('nav.billing') }}
        </x-jet-dropdown-link>

        @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
            <x-jet-dropdown-link href="{{ route('api-tokens.index') }}">
                {{ __('API Tokens') }}
            </x-jet-dropdown-link>
        @endif

        <div class="border-t border-gray-100"></div>

        {{-- Authentication --}}
        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <x-jet-dropdown-link
                href="{{ route('logout') }}"
                 onclick="
                    event.preventDefault();
                    this.closest('form').submit();
                "
            >
                {{ __('Log Out') }}
            </x-jet-dropdown-link>
        </form>
    </x-slot>
</x-jet-dropdown>
