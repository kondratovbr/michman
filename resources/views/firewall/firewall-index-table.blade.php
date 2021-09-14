{{--TODO: CRITICAL! Unfinished?--}}

<x-table-section>

    <x-slot name="title">{{ __('servers.firewall.table.title') }}</x-slot>

    <x-slot name="header">
        <x-tr-header>
            <x-th>{{ __('servers.firewall.table.name') }}</x-th>
            <x-th>{{ __('servers.firewall.table.port') }}</x-th>
            <x-th>{{ __('servers.firewall.table.type') }}</x-th>
            <x-th>{{ __('servers.firewall.table.from-ip') }}</x-th>
            <x-th></x-th>
        </x-tr-header>
    </x-slot>

    <x-slot name="body">
        @foreach($firewallRules as $rule)
            <x-tr>
                <x-td>{{ $rule->name }}</x-td>
                <x-td>{{ $rule->port }}</x-td>
                <x-td>
                    <x-badge colors="success">{{ __('servers.firewall.table.allow') }}</x-badge>
                </x-td>
                <x-td>{{ $rule->fromIp ?? __('servers.firewall.table.any') }}</x-td>
                <x-td class="flex justify-end items-center min-h-14">
                    @if($rule->isAdded() && $rule->canDelete)
                        <x-buttons.trash wire:click.prevent="delete('{{ $rule->getKey() }}')" />
                    @endif
                    @if($rule->isDeleting())
                        <x-buttons.trash :loading="true" />
                    @endif
                    @if($rule->isAdding())
                        <div class="mr-4">
                            <x-spinner/>
                        </div>
                    @endif
                </x-td>
            </x-tr>
        @endforeach
    </x-slot>

    @if($firewallRules->isEmpty())
        <x-slot name="empty">
            <p class="max-w-prose">{{ __('servers.firewall.table.empty') }}</p>
        </x-slot>
    @endif

</x-table-section>
