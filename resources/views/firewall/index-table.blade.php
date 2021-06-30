{{--TODO: CRITICAL! Unfinished!--}}

{{--TODO: CRITICAL! Make sure that the user is warned when they try to remove a critical firewall rule needed for an existing project to function.--}}

<x-table-section>

    <x-slot name="title">{{ __('servers.firewall.table.title') }}</x-slot>

    <x-slot name="header">
        <x-tr-header>
            <x-th>Name</x-th>
            <x-th>Port</x-th>
            <x-th>Type</x-th>
            <x-th>From IP</x-th>
            <x-th></x-th>
        </x-tr-header>
    </x-slot>

    <x-slot name="body">

        <x-tr>
            <x-td>
                <x-badge colors="success">Allow</x-badge>
            </x-td>
            <x-td>
                <x-badge colors="success">Allow</x-badge>
            </x-td>
            <x-td>
                <x-badge colors="success">Allow</x-badge>
            </x-td>
            <x-td>
                <x-badge colors="success">Allow</x-badge>
            </x-td>
            <x-td>
                <x-badge colors="success">Allow</x-badge>
            </x-td>
        </x-tr>

        @foreach($firewallRules as $rule)
            <x-tr>
                <x-td>{{ $rule->name }}</x-td>
                <x-td>{{ $rule->port }}</x-td>
                <x-td>
                    <x-badge colors="success">Allow</x-badge>
                </x-td>
                <x-td>{{ $rule->fromIp }}</x-td>
                <x-td>
                    <x-buttons.danger>X</x-buttons.danger>
                </x-td>
            </x-tr>
        @endforeach
    </x-slot>

</x-table-section>
