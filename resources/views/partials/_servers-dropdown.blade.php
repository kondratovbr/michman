{{-- TODO: IMPORTANT! Check how it looks with longer names and everything. --}}

<x-navbar.dropdown>
    <x-slot name="trigger">
        <div class="flex items-center space-x-2">
            <x-icon><i class="fa fa-server"></i></x-icon>
            <span>{{ __('nav.servers') }}</span>
        </div>
    </x-slot>
    <x-dropdown.menu>
        @foreach($user->servers as $server)
            <x-dropdown.link
                href="{{ route('servers.show', $server) }}"
                :capitalize="false"
            >
{{--                TODO: VERY IMPORTANT! Implement and enable this icon.--}}
{{--                TODO: This icon looks rather dull. Any better options? Maybe some effects or slight animations? Should google it.--}}
{{--                TODO: This icon should reflect server's status. Like, blink amber if something is wrong, for example. Also, maybe replace with a spinner when the server is in the process of creation and make the link disabled.--}}
{{--                <x-slot name="icon"><i class="fas fa-circle text-green-500 text-xs"></i></x-slot>--}}
                {{ $server->name }}
            </x-dropdown.link>
        @endforeach
    </x-dropdown.menu>
</x-navbar.dropdown>
