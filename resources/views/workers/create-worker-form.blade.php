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
                <x-label>{{ __('projects.queue.create.app.label') }}</x-label>
                <x-inputs.text
                    name="state.app"
                    wire:model="state.app"
                />
                <x-input-error for="state.app" />
                <x-help>{{ __('projects.queue.create.app.help') }}</x-help>
            </x-field>

            @if($state['type'] == 'celery')
                <x-field class="max-w-sm">
                    <x-label>{{ __('projects.queue.create.processes.label') }}</x-label>
                    <x-inputs.number
                        name="state.processes"
                        wire:model="state.processes"
                        min="1"
                        step="1"
                    />
                    <x-input-error for="state.processes" />
                    <x-help>{{ __('projects.queue.create.processes.help') }}</x-help>
                </x-field>

                <x-field class="max-w-sm">
                    <x-label>{{ __('projects.queue.create.queues.label') }}</x-label>
                    <x-inputs.text
                        name="state.queues"
                        wire:model="state.queues"
                    />
                    <x-input-error for="state.queues" />
                    <x-help>{{ __('projects.queue.create.queues.help') }}</x-help>
                </x-field>

                <x-field class="max-w-sm">
                    <x-label>{{ __('projects.queue.create.max-tasks.label') }}</x-label>
                    <x-inputs.number
                        name="state.max_tasks_per_child"
                        wire:model="state.max_tasks_per_child"
                        min="1"
                        step="1"
                    />
                    <x-input-error for="state.max_tasks_per_child" />
                    <x-help>{{ __('projects.queue.create.max-tasks.help') }}</x-help>
                </x-field>

                <x-field class="max-w-sm">
                    <x-label>{{ __('projects.queue.create.max-memory.label') }}</x-label>
                    <x-inputs.number
                        name="state.max_memory_per_child"
                        wire:model="state.max_memory_per_child"
                        min="1"
                        step="1"
                    />
                    <x-input-error for="state.max_memory_per_child" />
                    <x-help>{{ __('projects.queue.create.max-memory.help') }}</x-help>
                </x-field>

            @endif

            <x-field class="max-w-sm">
                <x-label>{{ __('projects.queue.create.stop-seconds.label') }}</x-label>
                <x-inputs.number
                    name="state.stop_seconds"
                    wire:model="state.stop_seconds"
                    min="1"
                    step="1"
                />
                <x-input-error for="state.stop_seconds" />
                <x-help>{{ __('projects.queue.create.stop-seconds.help') }}</x-help>
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
