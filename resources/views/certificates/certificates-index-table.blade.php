<x-table-section>

    <x-slot name="title">{{ __('servers.ssl.index.title') }}</x-slot>

    <x-slot name="header">
        <x-tr-header>
            <x-th>{{ __('servers.ssl.domains') }}</x-th>
            <x-th>{{ __('servers.ssl.type') }}</x-th>
            <x-th></x-th>
        </x-tr-header>
    </x-slot>

    <x-slot name="body">
        @foreach($certificates as $certificate)
            <x-tr>
                <x-td>{{ implode(', ', $certificate->domains) }}</x-td>
                <x-td>{{ __("servers.ssl.types.{$certificate->type}") }}</x-td>
                <x-td></x-td>
            </x-tr>
        @endforeach
    </x-slot>

    @if($certificates->isEmpty())
        <x-slot name="empty">{{ __('servers.ssl.index.empty') }}</x-slot>
    @endif

</x-table-section>
