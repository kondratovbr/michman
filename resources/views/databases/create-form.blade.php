{{--TODO: CRITICAL! Unfinished. Need loading animations, etc.--}}

<x-form-section submit="store">

    <x-slot name="title">{{ __('servers.database.form.title') }}</x-slot>

    <x-slot name="form">

        <x-field>
            <x-label>{{ __('servers.database.form.name') }}</x-label>
            <x-inputs.text
                name="name"
                wire:model.defer="name"
            />
            <x-input-error for="name" />
        </x-field>

        @if(! $databaseUsers->isEmpty())
            <x-field class="space-y-1">
                @foreach($databaseUsers as $databaseUser)
                    <x-checkbox-new
                        name="grantedUsers.{{ $databaseUser->getKey() }}"
                    >{{ $databaseUser->name }}</x-checkbox-new>
                @endforeach
            </x-field>
        @endif
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
