<x-table-section>

    <x-slot name="title">{{ __('servers.ssl.index.title') }}</x-slot>

    <x-slot name="header">
        <x-tr-header>
            <x-th>{{ __('servers.ssl.domains') }}</x-th>
            <x-th>{{ __('servers.ssl.type') }}</x-th>
            <x-th></x-th>
            <x-th></x-th>
        </x-tr-header>
    </x-slot>

    <x-slot name="body">
        @foreach($certificates as $cert)
            <x-tr>
                <x-td>{{ implode(', ', $cert->domains) }}</x-td>
                <x-td>{{ __("servers.ssl.types.{$cert->type}") }}</x-td>
                <x-td>
{{--                    TODO: Should I center these badges? Here and in all other tables.--}}
{{--                    TODO: CRITICAL! I changed it, so make sure it works.--}}
                    <x-state-badge :state="$cert->state" />
                </x-td>
                <x-td>
                    <div class="flex justify-end items-center">
                        @if($cert->isInstalled())
                            <x-buttons.trash
                                wire:click.prevent="delete('{{ $cert->getKey() }}')"
                                wire:loading.attr="disabled"
                            />
                        @else
                            <div class="mr-4.5">
                                <x-spinner/>
                            </div>
                        @endif
                    </div>
                </x-td>
            </x-tr>
        @endforeach
    </x-slot>

    @if($certificates->isEmpty())
        <x-slot name="empty">{{ __('servers.ssl.index.empty') }}</x-slot>
    @endif

</x-table-section>
