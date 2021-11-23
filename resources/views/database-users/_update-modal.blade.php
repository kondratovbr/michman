<x-modals.small wire:model="modalOpen" modalId="updateDatabaseUserModal">
    @if(! is_null($updatingUser ?? null))

        <x-slot name="header">
            <h3 class="text-lg font-medium">
                {{ __('servers.database-users.form.edit-user', ['name' => $updatingUser->name]) }}
            </h3>
        </x-slot>

        <x-slot name="content">
            <div class="space-y-6">

                <x-field class="max-w-sm">
                    <x-label>{{ __('servers.database-users.form.new-password') }}</x-label>
                    <x-inputs.text
                        name="password"
                        wire:model.defer="password"
                    />
                    <x-input-error for="password" />
                </x-field>

                @if(! $databases->isEmpty())
                    <x-field class="max-w-xs">
                        <x-label>{{ __('servers.database-users.form.grant-access') }}</x-label>
                        <div class="flex flex-col space-y-1">
                            @foreach($databases as $database)
                                <x-checkbox-new
                                    name="grantedDatabases.{{ $database->getKey() }}"
                                    wire:model.defer="grantedDatabases.{{ $database->getKey() }}"
                                    id="update-database-user-modal-database-{{ $database->getKey() }}"
                                >{{ $database->name }}</x-checkbox-new>
                            @endforeach
                        </div>
                    </x-field>
                @endif

            </div>
        </x-slot>

        <x-slot name="actions">
            <x-buttons>
                <x-buttons.primary
                    wire:click.prevent="update"
                    wire:loading.attr="disabled"
                >
                    {{ __('buttons.update') }}
                </x-buttons.primary>
                <x-buttons.secondary
                    x-on:click.prevent="show = false"
                    wire:loading.attr="disabled"
                >
                    {{ __('buttons.cancel') }}
                </x-buttons.secondary>
            </x-buttons>
        </x-slot>

    @endif
</x-modals.small>
