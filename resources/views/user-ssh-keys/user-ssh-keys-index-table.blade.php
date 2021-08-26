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
                <x-td></x-td>
                <x-td></x-td>
            </x-tr>
        @endforeach
    </x-slot>

    @if($keys->isEmpty())
        <x-slot name="empty">
            {{ __('account.ssh.empty') }}
        </x-slot>
    @endif

</x-table-section>
