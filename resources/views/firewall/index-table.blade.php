{{--TODO: CRITICAL! Unfinished!--}}

{{--TODO: IMPORTANT! Make sure that the user is warned when they try to remove a critical firewall rule needed for an existing project to function.--}}

{{--TODO: IMPORTANT! Add a loading animation when the rule is created until it is added and also when it is set to be deleted until it is actually deleted.--}}

{{--TODO: CRITICAL! CONTINUE! Test the adding and the deletion. Also, re-test the server creation part and update tests.--}}

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
                <x-td class="flex justify-end items-center">
                    @if($rule->isAdded() && $rule->canDelete)
                        <x-buttons.trash wire:click="delete('{{ $rule->getKey() }}')" />
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

</x-table-section>
