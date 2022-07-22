<x-dropdown.menu
    drop="up"
    align="right"
    minWidth="64"
    show="sub === 'account'"
>

    <x-dropdown.back-button/>

    <x-dropdown.link action="sub-page" subPage="profile" route="account.show">
        <x-slot name="icon"><i class="far fa-user"></i></x-slot>
        {{ __('account.profile.button') }}
    </x-dropdown.link>

    <x-dropdown.link action="sub-page" subPage="ssh" route="account.show">
        <x-slot name="icon"><i class="fa fa-terminal"></i></x-slot>
        {{ __('account.ssh.button') }}
    </x-dropdown.link>

    <x-dropdown.link action="sub-page" subPage="providers" route="account.show">
        <x-slot name="icon"><i class="fa fa-server"></i></x-slot>
        {{ __('account.providers.button') }}
    </x-dropdown.link>

    <x-dropdown.link action="sub-page" subPage="vcs" route="account.show">
        <x-slot name="icon"><i class="fa fa-code-branch"></i></x-slot>
        {{ __('account.vcs.button') }}
    </x-dropdown.link>

    {{-- TODO: Get this back on when it's implemented. --}}
{{--    <x-dropdown.link action="sub-page" subPage="api" route="account.show">--}}
{{--        TODO: Maybe put a Michman logo or some other icon here.--}}
{{--        <x-slot name="icon"><i class="fa fa-ship"></i></x-slot>--}}
{{--        {{ __('account.api.button') }}--}}
{{--    </x-dropdown.link>--}}

</x-dropdown.menu>
