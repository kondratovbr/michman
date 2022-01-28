{{--TODO: CRITICAL! Placeholders! Don't forget to put actual routes here!--}}
{{--TODO: IMPORTANT! Make sure longer emails actually fit. Same in the navbar. Maybe cut the email if it's long. Or maybe even don't show it at all if it's long - just an avatar.--}}

<x-dropdown.menu drop="up" align="right" minWidth="64" :header="true">

    <x-dropdown.header-title :capitalize="false" paddingLeft="pl-7.5">
        <div class="flex items-center space-x-2">
            <x-avatar class="h-10 w-10" />
            <span>{{ user()->email }}</span>
        </div>
    </x-dropdown.header-title>

    <x-dropdown.link action="account">
        <x-slot name="icon"><i class="far fa-user"></i></x-slot>
        {{ __('nav.account') }}
        <x-slot name="iconRight"><i class="fas fa-chevron-right text-gray-400"></i></x-slot>
    </x-dropdown.link>

    <x-dropdown.link href="/billing">
        <x-slot name="icon"><i class="far fa-money-bill-alt"></i></x-slot>
        {{ __('nav.billing') }}
    </x-dropdown.link>

    @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
        <x-dropdown.link href="{{ route('api-tokens.index') }}">
            <x-slot name="icon"><i class="fa fa-ship"></i></x-slot>
            {{ __('nav.api-tokens') }}
        </x-dropdown.link>
    @endif

    <x-dropdown.link href="" :external="true">
        <x-slot name="icon"><i class="far fa-file-alt"></i></x-slot>
        {{ __('nav.documentation') }}
        <x-slot name="iconRight"><i class="fas fa-external-link-alt text-gray-400"></i></x-slot>
    </x-dropdown.link>

    <x-dropdown.separator/>

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
