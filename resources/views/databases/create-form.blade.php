{{--TODO: CRITICAL! Unfinished. Need loading animations, etc.--}}

<x-form-section submit="store">

    <x-slot name="title">{{ __('servers.database.form.title') }}</x-slot>

    <x-slot name="form">
        <div class="space-y-6">

            <x-field class="max-w-sm">
                <x-label>{{ __('servers.database.form.name') }}</x-label>
                <x-inputs.text
                    name="name"
                    wire:model.defer="name"
                />
                <x-input-error for="name" />
            </x-field>

            @if(! $databaseUsers->isEmpty())
                <x-field class="max-w-xs">
                    <x-label>{{ __('servers.database.form.grant-access') }}</x-label>
                    <div class="flex flex-col space-y-1">
                        @foreach($databaseUsers as $databaseUser)
                            <x-checkbox-new
                                name="grantedUsers.{{ $databaseUser->getKey() }}"
                                wire:model.defer="grantedUsers.{{ $databaseUser->getKey() }}"
                                id="create-database-database-user-{{ $databaseUser->getKey() }}"
                            >{{ $databaseUser->name }}</x-checkbox-new>
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
            {{ __('servers.database.form.button') }}
        </x-buttons.primary>
    </x-slot>

</x-form-section>
