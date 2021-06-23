{{--TODO: CRITICAL! Unfinished!--}}

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
        @foreach($pythonVersions as $version)
            @php
                /** @var string $version */
                /** @var \Illuminate\Database\Eloquent\Collection $pythons */

                $python = $pythons->where('version', $version)->first()
            @endphp

            <x-tr>
                <x-td>
                    Python {{ __("servers.pythons.versions.{$version}") }}
                    <x-code>{{ config("servers.python.{$version}.cli") }}</x-code>
                </x-td>
                @if(! is_null($python))
                    <x-td><x-badge colors="success">{{ __('misc.installed') }}</x-badge></x-td>
                    <x-td></x-td>
                @else
                    <x-td>—</x-td>
                    <x-td>
                        <x-buttons.primary size="small">{{ __('buttons.install') }}</x-buttons.primary>
                    </x-td>
                @endif
            </x-tr>
        @endforeach
    </x-slot>

</x-table-section>
