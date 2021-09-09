<x-table-section>

    <x-slot name="title">{{ __('servers.daemons.index.title') }}</x-slot>

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
                <x-td><x-code>{{ $daemon->command }}</x-code></x-td>
                <x-td><x-code>{{ $daemon->directory }}</x-code></x-td>
                <x-td><x-state-badge :state="$daemon->state" /></x-td>
                <x-td>Menu!</x-td>
            </x-tr>
        @endforeach
    </x-slot>

    @if($daemons->isEmpty())
        <x-slot name="empty">
            <p class="max-w-prose">{{ __('servers.daemons.index.empty') }}</p>
        </x-slot>
    @endempty

</x-table-section>
