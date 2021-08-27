<x-table-section>

    <x-slot name="title"></x-slot>

    <x-slot name="header">
        <x-tr-header>
            <x-th>{{ __('account.ssh.name.title') }}</x-th>
            <x-th>{{ __('account.ssh.fingerprint.title') }}</x-th>
            <x-th></x-th>
            <x-th></x-th>
        </x-tr-header>
    </x-slot>

    <x-slot name="body">
        @foreach($keys as $key)
            <x-tr>
                <x-td>{{ $key->name }}</x-td>
                <x-td><x-code>{{ $key->publicKeyFingerprint }}</x-code></x-td>
                <x-td>
                    @if($key->addedToAllServers())
                        <div class="flex justify-center"><x-icon><i class="fas fa-check"></i></x-icon></div>
                    @else
                        <x-buttons.primary
                            wire:click.prevent="addToAllServers('{{ $key->getKey() }}')"
                            wire:loading.attr="disabled"
                            size="small"
                        >{{ __('account.ssh.add-to-servers') }}</x-buttons.primary>
                    @endif
                </x-td>
                <x-td>
                    <x-ellipsis-dropdown>
{{--                        TODO: CRITICAL! This menu is cut by the table's overflow, like all others. Fix and make the menu wider - the longer button doesn't fit.--}}
                        <x-dropdown.menu align="right">
                            <x-dropdown.button
                                wire:click.prevent="removeFromMichman('{{ $key->getKey() }}')"
                                wire:loading.attr="disabled"
                            >{{ __('account.ssh.delete') }}</x-dropdown.button>
                            <x-dropdown.button
                                wire:click.prevent="removeFromServers('{{ $key->getKey() }}')"
                                wire:loading.attr="disabled"
                            >{{ __('account.ssh.delete-and-remove') }}</x-dropdown.button>
                        </x-dropdown.menu>
                    </x-ellipsis-dropdown>
                </x-td>
            </x-tr>
        @endforeach
    </x-slot>

    @if($keys->isEmpty())
        <x-slot name="empty">
            {{ __('account.ssh.empty') }}
        </x-slot>
    @endif

</x-table-section>
