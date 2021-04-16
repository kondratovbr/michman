<x-table-section>

    <x-slot name="title">{{ __('servers.index.title') }}</x-slot>

    <x-slot name="header">
        <x-tr-header>
            <x-th>{{ __('servers.index.table.server') }}</x-th>
            <x-th>{{ __('servers.index.table.ip') }}</x-th>
            {{-- Badges, like "active" (has active projects) --}}
            <x-th></x-th>
            {{-- Buttons, like "edit" and maybe "refresh" --}}
            <x-th></x-th>
        </x-tr-header>
    </x-slot>

    <x-slot name="body">
        {{-- TODO: Check how it looks with longer names and everything. --}}
        @foreach($servers as $server)
            <x-tr>
                <x-td>{{ $server->name }}</x-td>
                <x-td></x-td>
                <x-td></x-td>
                <x-td></x-td>
            </x-tr>
        @endforeach
    </x-slot>

</x-table-section>
