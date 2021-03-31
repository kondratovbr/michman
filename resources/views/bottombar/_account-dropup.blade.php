{{--TODO: CRITICAL! Placeholders. Put actual routes here.--}}

<x-dropdown.back-button/>

<x-dropdown.link href="{{ route('profile.show') }}">
    <x-slot name="icon"><i class="far fa-user"></i></x-slot>
    {{ __('account.profile.button') }}
</x-dropdown.link>

<x-dropdown.link href="">
    <x-slot name="icon"><i class="fa fa-terminal"></i></x-slot>
    {{ __('account.ssh.button') }}
</x-dropdown.link>

<x-dropdown.link href="">
    <x-slot name="icon"><i class="fa fa-server"></i></x-slot>
    {{ __('account.providers.button') }}
</x-dropdown.link>

<x-dropdown.link href="">
    <x-slot name="icon"><i class="fa fa-code-branch"></i></x-slot>
    {{ __('account.vcs.button') }}
</x-dropdown.link>

<x-dropdown.link href="">
{{--    TODO: Maybe put a Michman logo or some other icon here.--}}
    <x-slot name="icon"><i class="fa fa-ship"></i></x-slot>
    {{ __('account.api.button') }}
</x-dropdown.link>
