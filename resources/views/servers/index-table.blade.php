{{--TODO: IMPORTANT! Make sure the index page itself is wide enough. I wanted to make it wider, since it doesn't have an aside menu. This will make the table fit better. Maybe even remove right side descriptions on this page to fit everything better. --}}

{{--TODO: CRITICAL! Unfinished! Add server statuses and actions. Like, refresh status. Also, implement a servers health check and use it for server statuses. --}}

<x-table-section>

    <x-slot name="title">{{ __('servers.index.title') }}</x-slot>

    <x-slot name="header">
        <x-tr-header>
            <x-th>{{ __('servers.index.table.server') }}</x-th>
            <x-th>{{ __('servers.index.table.ip') }}</x-th>
            <x-th>{{ __('servers.index.table.type') }}</x-th>
            {{-- Badges, like "active" (has active projects) --}}
            <x-th></x-th>
            {{-- Buttons, like "edit" and maybe "refresh" --}}
            <x-th></x-th>
        </x-tr-header>
    </x-slot>

    <x-slot name="body">
        {{-- TODO: IMPORTANT! Check how it looks with longer names and everything. --}}
        @foreach($servers as $server)
            <x-tr>
                <x-td>
                    <div class="flex items-center">
                        <x-icon class="text-2xl" size="8">
                            <i class="{{ config("providers.list.{$server->provider->provider}.icon") }}"></i>
                        </x-icon>
                        <div class="ml-2 flex flex-col">
{{--                            TODO: If the name is too long - cut it with the dots at the end.--}}
                            <x-app-link href="{{ route('servers.show', [$server, 'projects']) }}">{{ $server->name }}</x-app-link>
                            <p class="text-sm">Foobar</p>
                        </div>
                    </div>
                </x-td>
{{--                TODO: Implement "Click to copy" here, like on Forge.--}}
                <x-td>{{ $server->publicIp ?? __('misc.n/a') }}</x-td>
                <x-td>
                    <x-badge>{{ __("servers.types.{$server->type}.badge") }}</x-badge>
                </x-td>
                <x-td></x-td>
                <x-td></x-td>
            </x-tr>
        @endforeach
    </x-slot>

</x-table-section>
