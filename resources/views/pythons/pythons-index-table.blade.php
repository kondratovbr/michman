{{--TODO: This table (maybe others as well) jumps a bit when it refreshes and something changes. Seemingly because buttons change the height of rows and when there are no buttons the width of columns changes as well. Should fix those somehow so it doesn't jump. It also jumps when a badge width changes, which inevitably happens sometimes.--}}

{{--TODO: CRITICAL! Unfinished!--}}

{{--TODO: CRITICAL! Does deleting works? Do loading animation show up?--}}

{{--TODO: CRITICAL! Make sure that versions that are in use by projects cannot be removed.--}}

{{--TODO: CRITICAL! The table doesn't properly fit on some screen sizes. Breaks completely on smaller desktops. Should check it. Check other index tables as well.--}}

<x-table-section>

    <x-slot name="title">{{ __('servers.pythons.table.title') }}</x-slot>

    <x-slot name="description">{{ __('servers.pythons.table.description') }}</x-slot>

    <x-slot name="header">
        <x-tr-header>
            <x-th>{{ __('servers.pythons.table.version') }}</x-th>
            <x-th>{{ __('servers.pythons.table.cli') }}</x-th>
            <x-th>{{ __('servers.pythons.table.patch-version') }}</x-th>
            <x-th>{{ __('servers.pythons.table.status') }}</x-th>
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
                    <x-td class="min-w-14 flex justify-center items-center">
{{--                        TODO: Maybe make a generally smaller version of this dropdown. The paddings are a bit too big and disproportionte to the text.--}}
                        <x-ellipsis-dropdown :disabled="$python->status !== 'installed'">

                            <x-dropdown.menu align="right">
                                <x-dropdown.button
                                    class="text-sm"
                                    wire:click="patch('{{ $python->getKey() }}')"
                                    wire:loading.attr="disabled"
                                >
                                    {{ __('servers.pythons.table.patch-button') }}
                                </x-dropdown.button>
                                <x-dropdown.separator/>
                                <x-dropdown.button
                                    class="text-sm"
                                    wire:click="remove('{{ $python->getKey() }}')"
                                    wire:loading.attr="disabled"
                                >
                                    {{ __('servers.pythons.table.remove-button', ['version' => __("servers.pythons.versions.{$version}")]) }}
                                </x-dropdown.button>
                            </x-dropdown.menu>

                        </x-ellipsis-dropdown>
                    </x-td>
                @else
                    <x-td>—</x-td>
                    <x-td>—</x-td>
                    <x-td class="min-w-14 flex justify-center items-center">
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
