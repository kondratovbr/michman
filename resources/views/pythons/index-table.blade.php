<x-table-section>

    <x-slot name="title">{{ __('servers.pythons.table.title') }}</x-slot>

    <x-slot name="header">
        <x-tr-header>
            <x-th>{{ __('servers.pythons.table.version') }}</x-th>
            <x-th>{{ __('servers.pythons.table.status') }}</x-th>
            {{-- Buttons, like "install", "delete", "patch". --}}
            <x-th></x-th>
        </x-tr-header>
    </x-slot>

    <x-slot name="body">
{{--        TODO: CRITICAL! Don't forget - it should show ALL supported major versions, not only the installed ones. See how Forge does it.--}}
        @foreach($pythons as $python)
            <x-tr>
                <x-td>{{ __("servers.pythons.versions.{$python->version}") }}</x-td>
                <x-td></x-td>
                <x-td></x-td>
            </x-tr>
        @endforeach
    </x-slot>

</x-table-section>
