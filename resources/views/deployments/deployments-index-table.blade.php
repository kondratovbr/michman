<x-table-section>

    <x-slot name="title">{{ __('deployments.table.title') }}</x-slot>

    <x-slot name="header">
        <x-tr-header>
            <x-th></x-th>
            <x-th>{{ __('deployments.table.commit') }}</x-th>
            <x-th>{{ __('deployments.table.started-at') }}</x-th>
            <x-th>{{ __('deployments.table.status') }}</x-th>
            <x-th></x-th>
        </x-tr-header>
    </x-slot>

    <x-slot name="body">
        @foreach($deployments as $deployment)
            <x-tr>
                <x-td></x-td>
                <x-td>
                    <div class="flex flex-col items-start">
                        <x-app-link href="{{ $deployment->commitUrl }}" :external="true" :icon="false">{{ Str::substr($deployment->commit, 0, 8) }}</x-app-link>
                        <x-code size="small">{{ $deployment->branch }}</x-code>
                    </div>
                </x-td>
                <x-td>{{ $deployment->createdAtFormatted }}</x-td>
                <x-td>
                    <div class="flex flex-col items-start space-y-1">
                        <x-deployments.status-badge :deployment="$deployment" />
                        @if($deployment->finished)
                            <span class="text-xs">{{ $deployment->duration->forHumans() }}</span>
                        @endif
                    </div>
                </x-td>
                <x-td>
                    <x-ellipsis-dropdown :disabled="! $deployment->finished">
                        <x-dropdown.menu align="right">
                            @if($deployment->servers->count() == 1)
                                <x-dropdown.button
                                    class="text-sm"
                                    wire:click="showLog('{{ $deployment->getKey() }}', '{{ $deployment->servers->first()->getKey() }}')"
                                >
                                    {{ __('deployments.view-output') }}
                                </x-dropdown.button>
                            @else
                                <x-dropdown.title>
                                    {{ __('deployments.view-output-from-server') }}
                                </x-dropdown.title>
                                @foreach($deployment->servers as $server)
                                    <x-dropdown.button
                                        class="text-sm"
                                        wire:click="showLog('{{ $deployment->getKey() }}', '{{ $server->getKey() }}')"
                                    >
                                        {{ $server->name }}
                                    </x-dropdown.button>
                                @endforeach
                            @endif
                        </x-dropdown.menu>
                    </x-ellipsis-dropdown>
                </x-td>
            </x-tr>
        @endforeach
    </x-slot>

    @if($modalOpen)
        <x-slot name="modal">
            @include('deployments._log-modal')
        </x-slot>
    @endif

    @if($deployments->isEmpty())
        <x-slot name="empty">
            <p class="max-w-prose">{{ __('deployments.table.empty') }}</p>
        </x-slot>
    @endif

    <x-slot name="actions">
        <div class="flex items-center space-x-3">
            <x-buttons.primary
                wire:click.prevent="deploy"
            >{{ __('deployments.deploy-button') }}</x-buttons.primary>
            <div class="text-sm">Deploying the <x-code>{{ $project->branch }}</x-code> branch</div>
        </div>
    </x-slot>

</x-table-section>
