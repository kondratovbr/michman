{{--TODO: CRITICAL! Values/links are placeholders. Don't forget to put the actual ones.--}}
{{--TODO: CRITICAL! Make sure the irrelevant buttons aren't shown and menus cannot be accessed. Based on the server's type.--}}

{{--TODO: IMPORTANT. Try rebuilding this system with @entangle - maybe it will help with "back"/"front" buttons in the browser. Also - needs some loading indication and skeleton placeholders. The pages may time a bit of time to load on slower connections.--}}

<aside x-data="{ current: '{{ $this->show }}' }">
    <ul class="flex flex-col items-stretch text-gray-200">

        <x-menu.item show="projects">
{{--            TODO: ICON! This is a placeholder icon. The actual one (fas fa-browser) comes only in FontAwesome Pro. Should pay for it and use the icon. Maybe some other places required paid icons as well.--}}
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

        <x-menu.item show="daemons">
            <x-slot name="icon"><i class="fas fa-sync"></i></x-slot>
            {{ __('servers.daemons.button') }}
        </x-menu.item>

    </ul>
</aside>
