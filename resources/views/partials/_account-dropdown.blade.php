<x-navbar.dropdown>

{{--    TODO: CRITICAL! Don't forget to put real routes here!--}}

    <x-slot name="trigger">
        <div class="flex items-center space-x-2">
            <x-avatar class="h-8 w-8" />
            <span>{{ user()->email }}</span>
        </div>
    </x-slot>

    <x-dropdown.menu align="right">

        {{-- Account Management --}}
        <x-dropdown.title>
            {{ __('nav.manage_account') }}
        </x-dropdown.title>

        <x-dropdown.link href="{{ route('account.show', 'profile') }}">
            <x-slot name="icon"><i class="far fa-user"></i></x-slot>
            {{ __('nav.account') }}
        </x-dropdown.link>

        <x-dropdown.link href="">
            <x-slot name="icon"><i class="far fa-money-bill-alt"></i></x-slot>
            {{ __('nav.billing') }}
        </x-dropdown.link>

        @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
            <x-dropdown.link href="{{ route('api-tokens.index') }}">
                <x-slot name="icon"><i class="fa fa-ship"></i></x-slot>
                {{ __('nav.api-tokens') }}
            </x-dropdown.link>
        @endif

        <x-dropdown.separator/>

        {{-- Logout button --}}
        <x-form method="POST" action="{{ route('logout') }}" x-data="{}" x-ref="form">
            <x-dropdown.link
                x-on:click.prevent="$refs.form.submit()"
                role="button"
            >
                <x-slot name="icon"><i class="fa fa-sign-out-alt fa-flip-horizontal"></i></x-slot>
                {{ __('auth.logout') }}
            </x-dropdown.link>
        </x-form>

    </x-dropdown.menu>

</x-navbar.dropdown>
