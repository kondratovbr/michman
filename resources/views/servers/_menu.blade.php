{{--TODO: CRITICAL! Values/links are placeholders. Don't forget to put the actual ones.--}}
{{--TODO: CRITICAL! Make sure the irrelevant buttons aren't shown and menus cannot be accessed. Based on the server's type.--}}

<aside x-data="{ current: '{{ $this->show }}' }">
    <ul class="flex flex-col items-stretch text-gray-200">

        <x-menu.item show="projects">
{{--            TODO: CRITICAL! This is a placeholder icon. The actual one (fas fa-browser) comes only in FontAwesome Pro. Should pay for it and use the icon. Maybe some other places required paid icons as well.--}}
            <x-slot name="icon"><i class="fas fa-square"></i></x-slot>
            {{ __('servers.projects.button') }}
        </x-menu.item>

        <x-menu.item show="databases">
            <x-slot name="icon"><i class="fas fa-database"></i></x-slot>
            {{ __('servers.database.button') }}
        </x-menu.item>

        <x-menu.item show="pythons">
            <x-slot name="icon"><i class="fab fa-python"></i></x-slot>
            {{ __('servers.pythons.button') }}
        </x-menu.item>

        <x-menu.item show="firewall">
            <x-slot name="icon"><i class="fas fa-door-open"></i></x-slot>
            {{ __('servers.firewall.button') }}
        </x-menu.item>

        <x-menu.item show="ssl">
            <x-slot name="icon"><i class="fas fa-shield-alt"></i></x-slot>
            {{ __('servers.ssl.button') }}
        </x-menu.item>

    </ul>
</aside>
