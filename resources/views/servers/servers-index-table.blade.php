{{--TODO: CRITICAL! Unfinished! Add server statuses and actions. The short description is still "Foobar". Like, refresh status. Also, implement a servers health check and use it for server statuses. --}}

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
        @foreach($servers as $server)
            <x-tr>
                <x-td>
                    <div class="flex items-center">
                        <x-icon class="text-2xl" size="8">
                            <i class="{{ config("providers.list.{$server->provider->provider}.icon") }}"></i>
                        </x-icon>
                        <div class="ml-2 flex flex-col">
                            <x-app-link href="{{ route('servers.show', $server) }}">
                                {{ $server->name }}
                            </x-app-link>
                            <p class="text-sm">{{ $server->shortInfo() }}</p>
                        </div>
                    </div>
                </x-td>
                <x-td>
                    @isset($server->publicIp)
                        <x-clipboard>{{ $server->publicIp }}</x-clipboard>
                    @else
                        {{ __('misc.n/a') }}
                    @endisset
                </x-td>
                <x-td><x-badge>{{ __("servers.types.{$server->type}.badge") }}</x-badge></x-td>
{{--                TODO: The loading spinner makes the table to jump a little. Maybe ignore it - we will redesing the servers index anyway.--}}
                <x-td><x-state-badge :state="$server->state" /></x-td>
                <x-td>
                    <div class="bg-red-200">
                        Foobar! Should be red.
                    </div>
                </x-td>
            </x-tr>
        @endforeach
    </x-slot>

</x-table-section>
