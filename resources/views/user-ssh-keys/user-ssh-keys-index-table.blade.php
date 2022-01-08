{{--TODO: IMPORTANT! Don't forget to add a link to docs about where to get these keys. Need to be newbie-friendly.--}}

<x-table-section>

    <x-slot name="title"></x-slot>

    <x-slot name="header">
        <x-tr-header>
            <x-th>{{ __('account.ssh.name.label') }}</x-th>
            <x-th show="2xl">{{ __('account.ssh.fingerprint.label') }}</x-th>
            <x-th></x-th>
            <x-th></x-th>
        </x-tr-header>
    </x-slot>

    <x-slot name="body">
        @foreach($keys as $key)
            <x-tr>
                <x-td>{{ $key->name }}</x-td>
                <x-td show="2xl"><x-code>{{ $key->publicKeyFingerprint }}</x-code></x-td>
                <x-td class="w-16">
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
                <x-td class="w-16">
                    <x-ellipsis-dropdown>
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
