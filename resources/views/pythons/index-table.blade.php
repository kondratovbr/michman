{{--TODO: This table (maybe others as well) jumps a bit when it refreshes and something changes. Seemingly because buttons change the height of rows and when there are no buttons the width of columns changes as well. Should fix those somehow so it doesn't jump.--}}

{{--TODO: CRITICAL! Unfinished!--}}

{{--TODO: CRITICAL! Make sure that versions that are in use by projects cannot be removed.--}}

{{--TODO: IMPORTANT! The table doesn't properly fit on some screen sizes. Should check it.--}}

<x-table-section>

    <x-slot name="title">{{ __('servers.pythons.table.title') }}</x-slot>

    <x-slot name="description">{{ __('servers.pythons.table.description') }}</x-slot>

    <x-slot name="header">
        <x-tr-header>
            <x-th>{{ __('servers.pythons.table.version') }}</x-th>
            <x-th>{{ __('servers.pythons.table.cli') }}</x-th>
            <x-th>{{ __('servers.pythons.table.patch-version') }}</x-th>
            <x-th>{{ __('servers.pythons.table.status') }}</x-th>
            {{-- Buttons, like "install", "delete", "patch". --}}
            <x-th></x-th>
        </x-tr-header>
    </x-slot>

    <x-slot name="body">
        @foreach($pythonVersions as $version)
            @php
                /** @var string $version */
                /** @var \Illuminate\Database\Eloquent\Collection $pythons */

                $python = $pythons->where('version', $version)->first()
            @endphp

            <x-tr>
                <x-td>{{ spaceToNbsp('Python ' . __("servers.pythons.versions.{$version}")) }}</x-td>
                <x-td><x-code class="text-sm">{{ config("servers.python.{$version}.cli") }}</x-code></x-td>
                @if(! is_null($python))
                    <x-td>{{ $python->patchVersion }}</x-td>
                    <x-td><x-pythons.status-badge :python="$python" /></x-td>
                    <x-td class="min-w-64">
                        <x-buttons.ellipsis wire:loading.attr="disabled" />
                    </x-td>
                @else
                    <x-td>—</x-td>
                    <x-td>—</x-td>
                    <x-td class="min-w-64">
                        <x-buttons.primary
                            size="small"
                            wire:click="install('{{ $version }}')"
                            wire:loading.attr="disabled"
                        >{{ __('buttons.install') }}</x-buttons.primary>
                    </x-td>
                @endif
            </x-tr>
        @endforeach
    </x-slot>

</x-table-section>
