<x-form-section submit="store">

    <x-slot name="title">{{ __('servers.database-users.form.title') }}</x-slot>

    <x-slot name="form">
        <div class="space-y-6">
            <x-field class="max-w-sm">
                <x-label>{{ __('servers.database-users.form.name') }}</x-label>
                <x-inputs.text
                    name="name"
                    wire:model.defer="name"
                />
                <x-input-error for="name" />
            </x-field>

            <x-field class="max-w-sm">
                <x-label>{{ __('servers.database-users.form.password') }}</x-label>
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
                                id="create-database-user-database-{{ $database->getKey() }}"
                            >{{ $database->name }}</x-checkbox-new>
                        @endforeach
                    </div>
                </x-field>
            @endif
        </div>
    </x-slot>

    <x-slot name="actions">
        <x-buttons.primary
            wire:click.prevent="store"
            wire:loading.attr="disabled"
        >
            {{ __('servers.database-users.form.button') }}
        </x-buttons.primary>
    </x-slot>

</x-form-section>
