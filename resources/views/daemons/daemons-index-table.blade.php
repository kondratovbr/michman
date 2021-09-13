<x-table-section>

    <x-slot name="title">{{ __('servers.daemons.index.title') }}</x-slot>

    <x-slot name="titleActions">
        <x-buttons.secondary
            wire:click="updateStatuses"
            wire:loading.attr="disabled"
        >{{ __('servers.daemons.index.update-statuses') }}</x-buttons.secondary>
    </x-slot>

    <x-slot name="header">
        <x-tr-header>
            <x-th>{{ __('servers.daemons.command.label') }}</x-th>
            <x-th>{{ __('servers.daemons.directory.label') }}</x-th>
            <x-th></x-th>
            <x-th></x-th>
        </x-tr-header>
    </x-slot>

    <x-slot name="body">
        @foreach($daemons as $daemon)
            <x-tr>
                <x-td><x-code-block :wrap="true">{{ $daemon->command }}</x-code-block></x-td>
                <x-td><x-code-block :wrap="true">{{ $daemon->directory }}</x-code-block></x-td>
                <x-td><x-state-badge :state="$daemon->state" /></x-td>
                <x-td>
                    <x-ellipsis-dropdown>
                        <x-dropdown.menu align="right">
                            <x-dropdown.button
                                class="text-sm"
                                wire:click="start('{{ $daemon->getKey() }}')"
                                wire:loading.attr="disabled"
                            >
                                {{ __('servers.daemons.index.start') }}
                            </x-dropdown.button>
                            <x-dropdown.button
                                class="text-sm"
                                wire:click="stop('{{ $daemon->getKey() }}')"
                                wire:loading.attr="disabled"
                            >
                                {{ __('servers.daemons.index.stop') }}
                            </x-dropdown.button>
                            <x-dropdown.button
                                class="text-sm"
                                wire:click="restart('{{ $daemon->getKey() }}')"
                                wire:loading.attr="disabled"
                            >
                                {{ __('servers.daemons.index.restart') }}
                            </x-dropdown.button>
                            <x-dropdown.separator/>
                            <x-dropdown.button
                                class="text-sm"
                                wire:click="delete('{{ $daemon->getKey() }}')"
                                wire:loading.attr="disabled"
                            >
                                {{ __('servers.daemons.index.delete') }}
                            </x-dropdown.button>
                        </x-dropdown.menu>
                    </x-ellipsis-dropdown>
                </x-td>
            </x-tr>
        @endforeach
    </x-slot>

    @if($daemons->isEmpty())
        <x-slot name="empty">
            <p class="max-w-prose">{{ __('servers.daemons.index.empty') }}</p>
        </x-slot>
    @endempty

</x-table-section>
