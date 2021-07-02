{{--TODO: CRITICAL! Unfinished!--}}

<x-table-section>

    <x-slot name="title">{{ __('servers.database.table.title') }}</x-slot>

    <x-slot name="header">
        <x-tr-header>
            <x-th>{{ __('servers.database.table.name') }}</x-th>
            <x-th></x-th>
        </x-tr-header>
    </x-slot>

    <x-slot name="body">
        @foreach($databases as $database)
            <x-tr>
                <x-td>{{ $database->name }}</x-td>
                <x-td></x-td>
            </x-tr>
        @endforeach
    </x-slot>

    @if($databases->isEmpty())
        <x-slot name="empty">
            <p class="max-w-prose">{{ __('servers.database.table.empty') }}</p>
        </x-slot>
    @endif

</x-table-section>
