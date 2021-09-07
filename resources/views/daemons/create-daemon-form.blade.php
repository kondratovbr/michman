{{--TODO: IMPORTANT! Forms like this (and I have many) can and should be made better. The problem is that number fields are too wide - they don't reflect their intended content. Maybe try making them really narrow and putting the help messages to the side. Or google some better form layout ideas.--}}

<x-form-section submit="store">

    <x-slot name="title">{{ __('servers.daemons.create.title') }}</x-slot>

    <x-slot name="description">{{ __('servers.daemons.create.description') }}</x-slot>

    <x-slot name="form">
        <div class="space-y-6">

            <x-field>
                <x-label>{{ __('servers.daemons.command.label') }}</x-label>
                <x-inputs.text
                    name="state.command"
                    wire:model="state.command"
                    placeholder="python manage.py runserver"
                />
                <x-input-error for="state.command" />
{{--                TODO: IMPORTANT! This probably needs some more explaining in the docs.--}}
                <x-help>{{ __('servers.daemons.command.help') }}</x-help>
            </x-field>

{{--            TODO: IMPORTANT! Make this a select with autocomplete and a possible custom value. My search-select with some small tweaks should do.--}}
            <x-field class="max-w-sm">
                <x-label>{{ __('servers.daemons.username.label') }}</x-label>
                <x-inputs.text
                    name="state.username"
                    wire:model="state.username"
                />
                <x-input-error for="state.username" />
            </x-field>

            <x-field>
                <x-label>{{ __('servers.daemons.directory.label') }}</x-label>
                <x-inputs.text
                    name="state.directory"
                    wire:model="state.directory"
                />
                <x-input-error for="state.directory" />
                <x-help>{{ __('servers.daemons.directory.help') }}</x-help>
            </x-field>

            <x-field class="max-w-sm">
                <x-label>{{ __('servers.daemons.processes.label') }}</x-label>
                <x-inputs.number
                    name="state.processes"
                    wire:model="state.processes"
                    min="1"
                    step="1"
                />
                <x-input-error for="state.processes" />
                <x-help>{{ __('servers.daemons.processes.help') }}</x-help>
            </x-field>

            <x-field class="max-w-sm">
                <x-label>{{ __('servers.daemons.start-seconds.label') }}</x-label>
                <x-inputs.number
                    name="state.start_seconds"
                    wire:model="state.start_seconds"
                    min="1"
                    step="1"
                />
                <x-input-error for="state.start_seconds" />
                <x-help>{{ __('servers.daemons.start-seconds.help') }}</x-help>
            </x-field>

        </div>
    </x-slot>

    <x-slot name="actions">
        <x-buttons.primary
            wire:click.prevent="store"
            wire:loading.attr="disabled"
        >
            {{ __('servers.daemons.create.button') }}
        </x-buttons.primary>
    </x-slot>

</x-form-section>
