<x-form-section submit="store">

    <x-slot name="title">{{ __('projects.queue.create.title') }}</x-slot>

    <x-slot name="form">
        <div class="space-y-6">

            <x-field class="max-w-sm">
                <x-label>{{ __('projects.queue.create.type.label') }}</x-label>
                <x-select
                    name="state.type"
                    :options="$this->types"
                    :default="true"
                    wire:model="state.type"
                    wire:key="select-type"
                />
                <x-input-error for="state.type" />
            </x-field>

            @if(count($this->servers) > 1)
                <x-field class="max-w-sm">
                    <x-label>{{ __('projects.queue.create.server.label') }}</x-label>
                    <x-select
                        name="state.serverId"
                        :options="$this->servers"
                        :default="true"
                        wire:model="state.serverId"
                        wire:key="select-server"
                    />
                    <x-input-error for="state.serverId" />
                    <x-help>{{ __('projects.queue.create.server.help') }}</x-help>
                </x-field>
            @endif

            <x-field class="max-w-sm">
                <x-label></x-label>

                <x-input-error for="state.processes" />
            </x-field>

        </div>
    </x-slot>

    <x-slot name="actions">
        <x-buttons.primary
            wire:click.prevent="store"
            wire:loading.attr="disabled"
        >
            {{ __('projects.queue.create.button') }}
        </x-buttons.primary>
    </x-slot>

</x-form-section>
