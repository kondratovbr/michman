{{--TODO: IMPORTANT! Is "Sync Users" function (like in Forge) necessary?--}}

<x-table-section>

    <x-slot name="title">{{ __('servers.database-users.table.title') }}</x-slot>

    <x-slot name="header">
        <x-tr-header>
            <x-th>{{ __('servers.database-users.table.name') }}</x-th>
            <x-th></x-th>
        </x-tr-header>
    </x-slot>

    <x-slot name="body">
        @foreach($databaseUsers as $databaseUser)
            <x-tr>
                <x-td>{{ $databaseUser->name }}</x-td>
                <x-td>
                    <div class="flex justify-end items-center">
                        @if($databaseUser->tasks > 0)
                            <div class="mr-4.5">
                                <x-spinner/>
                            </div>
                        @else
                            <x-buttons.edit wire:click="openModal('{{ $databaseUser->getKey() }}')" />
{{--                            TODO: Maybe try reading user's ENV variables looking for database user's names to check if a user in use.--}}
                            <x-buttons.trash
                                class="ml-2"
                                wire:click.prevent="delete('{{ $databaseUser->getKey() }}')"
                                wire:key="delete-database-user-button-{{ $databaseUser->getKey() }}"
                            />
                        @endif
                    </div>
                </x-td>
            </x-tr>
        @endforeach

    </x-slot>

    <x-slot name="modal">
        @include('database-users._update-modal')
    </x-slot>

    @if($databaseUsers->isEmpty())
        <x-slot name="empty">
            <p class="max-w-prose">{{ __('servers.database-users.table.empty') }}</p>
        </x-slot>
    @endif

</x-table-section>
