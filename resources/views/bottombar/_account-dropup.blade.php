{{--TODO: CRITICAL! Placeholders. Put actual routes here.--}}

<x-dropdown.menu
    drop="up"
    align="right"
    minWidth="64"
    show="sub === 'account'"
>

    <x-dropdown.back-button/>

    <x-dropdown.link action="sub-page" subPage="profile">
        <x-slot name="icon"><i class="far fa-user"></i></x-slot>
        {{ __('account.profile.button') }}
    </x-dropdown.link>

    <x-dropdown.link action="sub-page" subPage="ssh">
        <x-slot name="icon"><i class="fa fa-terminal"></i></x-slot>
        {{ __('account.ssh.button') }}
    </x-dropdown.link>

    <x-dropdown.link action="sub-page" subPage="providers">
        <x-slot name="icon"><i class="fa fa-server"></i></x-slot>
        {{ __('account.providers.button') }}
    </x-dropdown.link>

    <x-dropdown.link action="sub-page" subPage="vcs">
        <x-slot name="icon"><i class="fa fa-code-branch"></i></x-slot>
        {{ __('account.vcs.button') }}
    </x-dropdown.link>

    <x-dropdown.link action="sub-page" subPage="api">
{{--        TODO: Maybe put a Michman logo or some other icon here.--}}
        <x-slot name="icon"><i class="fa fa-ship"></i></x-slot>
        {{ __('account.api.button') }}
    </x-dropdown.link>

{{--    TODO: CRITICAL! Don't forget to remove this!--}}
    <x-dropdown.link action="sub-page" subPage="foobar">
        <x-slot name="icon"><i class="fa fa-poo"></i></x-slot>
        Foobar
    </x-dropdown.link>

</x-dropdown.menu>
