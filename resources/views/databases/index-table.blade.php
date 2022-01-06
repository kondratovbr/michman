{{--TODO: IMPORTANT! Is "Sync Databases" function (like in Forge) necessary?--}}

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
                <x-td class="flex justify-end items-center min-h-14">
                    @if($database->tasks > 0)
                        <div class="mr-4.5">
                            <x-spinner/>
                        </div>
                    @else
{{--                        TODO: Maybe try reading user's ENV variables looking for database names to check if a database in use.--}}
                        <x-buttons.trash
                            wire:click.prevent="delete('{{ $database->getKey() }}')"
                            wire:key="delete-database-button-{{ $database->getKey() }}"
                            :disabled="Gate::denies('delete', $database)"
                        />
                    @endif
                </x-td>
            </x-tr>
        @endforeach
    </x-slot>

    @if($databases->isEmpty())
        <x-slot name="empty">
            <p class="max-w-prose">{{ __('servers.database.table.empty') }}</p>
        </x-slot>
    @endif

</x-table-section>
