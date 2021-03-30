<x-dropdown.header x-on:click.prevent="open = true; sub = ''">
    <x-slot name="icon"><i class="fas fa-arrow-left text-gray-400"></i></x-slot>
    {{ __('buttons.back') }}
</x-dropdown.header>



<x-dropdown.link>
    <x-slot name="icon"><i class="far fa-user"></i></x-slot>
    {{ __('account.profile.button') }}
</x-dropdown.link>

<x-dropdown.link>
    <x-slot name="icon"><i class="fa fa-terminal"></i></x-slot>
    {{ __('account.ssh.button') }}
</x-dropdown.link>

<x-dropdown.link>
    <x-slot name="icon"><i class="fa fa-server"></i></x-slot>
    {{ __('account.providers.button') }}
</x-dropdown.link>

<x-dropdown.link>
    <x-slot name="icon"><i class="fa fa-code-branch"></i></x-slot>
    {{ __('account.vcs.button') }}
</x-dropdown.link>

<x-dropdown.link>
{{--    TODO: Maybe put a Michman logo or some other icon here.--}}
    <x-slot name="icon"><i class="fa fa-ship"></i></x-slot>
    {{ __('account.api.button') }}
</x-dropdown.link>
