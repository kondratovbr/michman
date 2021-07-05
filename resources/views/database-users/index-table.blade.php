{{--TODO: CRITICAL! Unfinished! Editing is now implemented at all!--}}
{{--TODO: CRITICAL! Make sure a user that is being used by an active project cannot be deleted.--}}
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
                <x-td class="flex justify-end items-center min-h-14">
                    @if($databaseUser->isCreated())
                        <x-buttons.edit/>
                        <x-buttons.trash
                            wire:click="delete('{{ $databaseUser->getKey() }}')"
                            wire:key="delete-database-user-button-{{ $databaseUser->getKey() }}"
                        />
                    @endif
                    @if($databaseUser->isDeleting())
                        <x-buttons.edit disabled />
                        <x-buttons.trash
                            :loading="true"
                            wire:key="delete-database-user-button-{{ $databaseUser->getKey() }}"
                        />
                    @endif
                    @if($databaseUser->isCreating())
                        <div class="mr-4.5">
                            <x-spinner/>
                        </div>
                    @endif
                </x-td>
            </x-tr>
        @endforeach
    </x-slot>

    @if($databaseUsers->isEmpty())
        <x-slot name="empty">
            <p class="max-w-prose">{{ __('servers.database-users.table.empty') }}</p>
        </x-slot>
    @endif

</x-table-section>