<x-table-section>

    <x-slot name="title">{{ __('projects.queue.index.title') }}</x-slot>

    <x-slot name="header">
        <x-tr-header>
            <x-th>{{ __('projects.queue.create.type.label') }}</x-th>
            <x-th>{{ __('projects.queue.create.server.label') }}</x-th>
            <x-th>{{ __('projects.queue.create.queues.label') }}</x-th>
            <x-th>{{ __('projects.queue.create.processes.table') }}</x-th>
            <x-th>{{ __('misc.status') }}</x-th>

            <x-th></x-th>
        </x-tr-header>
    </x-slot>

    <x-slot name="body">
        @foreach($workers as $worker)
            <x-tr>
                <x-td>{{ __("projects.queue.types.{$worker->type}") }}</x-td>
{{--                TODO: CRITICAL! Make this a link.--}}
                <x-td>{{ $worker->server->name }}</x-td>
{{--                TODO: CRITICAL! Make sure to support other types in here.--}}
                <x-td>{{ implode(', ', $worker->queues ?? ['Celery']) }}</x-td>
                <x-td>{{ $worker->processes ?? 'Default' }}</x-td>
                <x-td><x-workers.status-badge :worker="$worker" /></x-td>

                <x-td>
                    @if($worker->isStarting())
                        <div class="flex justify-center items-center">
                            <x-spinner />
                        </div>
                    @else
                        <div class="flex justify-end items-center space-x-2">
                            <x-buttons.secondary
                                wire:click.prevent="restart('{{ $worker->getKey() }}')"
                                wire:loading.attr="disabled"
                                :disabled="$worker->isDeleting()"
                            >
{{--                                TODO: CRITICAL! Have I already implemented a button with an icon earlier? The icon should be positioned a bit to the left (maybe just a smaller left margin would do). See if I've done it before.--}}
                                <x-icon><i class="fas fa-redo"></i></x-icon>
                                <span class="ml-1.5">{{ __('misc.restart') }}</span>
                            </x-buttons.secondary>
                            <x-buttons.trash
                                wire:click.prevent="delete('{{ $worker->getKey() }}')"
                                wire:loading.attr="disabled"
                                :loading="$worker->isDeleting()"
                            />
                        </div>
                    @endif
                </x-td>
            </x-tr>
        @endforeach
    </x-slot>

    @if($workers->isEmpty())
        <x-slot name="empty">
            <p class="max-w-prose">{{ __('projects.queue.index.empty') }}</p>
        </x-slot>
    @endif

</x-table-section>
