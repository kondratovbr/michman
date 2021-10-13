<x-table-section>

    <x-slot name="title">{{ __('projects.queue.index.title') }}</x-slot>

    <x-slot name="titleActions">
        <x-buttons.secondary
            wire:click.prevent="updateStatuses"
            wire:loading.attr="disabled"
        >{{ __('projects.queue.update-statuses') }}</x-buttons.secondary>
    </x-slot>

    <x-slot name="header">
        <x-tr-header>
            <x-th>{{ __('projects.queue.create.type.label') }}</x-th>
            <x-th>{{ __('projects.queue.create.server.label') }}</x-th>
            <x-th>{{ __('projects.queue.create.queues.label') }}</x-th>
            <x-th show="lg">{{ __('projects.queue.create.processes.table') }}</x-th>
            <x-th>{{ __('misc.status') }}</x-th>
            <x-th></x-th>
        </x-tr-header>
    </x-slot>

    <x-slot name="body">
        @foreach($workers as $worker)
            <x-tr>
                <x-td>{{ __("projects.queue.types.{$worker->type}") }}</x-td>
                <x-td><x-app-link href="{{ route('servers.show', $worker->server) }}">{{ $worker->server->name }}</x-app-link></x-td>
{{--                TODO: CRITICAL! Make sure to support other types in here.--}}
                <x-td>{{ implode(', ', $worker->queues ?? ['Celery']) }}</x-td>
                <x-td show="lg">{{ $worker->processes ?? 'Auto' }}</x-td>
                <x-td><x-state-badge :state="$worker->state" /></x-td>

                <x-td>
                    @if($worker->isStarting())
                        <div class="flex justify-center items-center">
                            <x-spinner />
                        </div>
                    @else
                        <x-ellipsis-dropdown>
                            <x-dropdown.menu align="right">
                                <x-dropdown.button
                                    class="text-sm"
                                    wire:click.stop="showLog('{{ $worker->getKey() }}')"
                                    wire:loading.attr="disabled"
                                    x-data="{}"
                                    x-on:click.stop="$dispatch('open-modal')"
                                >
                                    <x-icon><i class="fas fa-eye"></i></x-icon>
                                    <span class="ml-1">{{ __('projects.queue.view-log-button') }}</span>
                                </x-dropdown.button>
                                <x-dropdown.button
                                    class="text-sm"
                                    wire:click.prevent="restart('{{ $worker->getKey() }}')"
                                    wire:loading.attr="disabled"
                                    :disabled="$worker->isDeleting()"
                                >
                                    <x-icon><i class="fas fa-redo"></i></x-icon>
                                    <span class="ml-1">{{ __('misc.restart') }}</span>
                                </x-dropdown.button>
                                <x-dropdown.button
                                    class="text-sm"
                                    wire:click.prevent="delete('{{ $worker->getKey() }}')"
                                    wire:loading.attr="disabled"
                                    :loading="$worker->isDeleting()"
                                >
                                    <x-icon><i class="fas fa-trash"></i></x-icon>
                                    <span class="ml-1">{{ __('misc.delete') }}</span>
                                </x-dropdown.button>
                            </x-dropdown.menu>
                        </x-ellipsis-dropdown>
                    @endif
                </x-td>
            </x-tr>
        @endforeach
    </x-slot>

    <x-slot name="modal">
        @include('workers._log-modal')
    </x-slot>

    @if($workers->isEmpty())
        <x-slot name="empty">
            <p class="max-w-prose">{{ __('projects.queue.index.empty') }}</p>
        </x-slot>
    @endif

</x-table-section>
