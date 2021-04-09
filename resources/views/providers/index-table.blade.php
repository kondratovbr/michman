{{--TODO: IMPORTANT! Add badges and buttons, implement removing providers. Note that stackable buttons shouldn't be very rounded.--}}
{{--TODO: Figure out how to verify that API is still accessible by a credentials and highlight/notify when it isn't.--}}

<x-table-section>
    <x-slot name="title">Server Providers</x-slot>

    <x-slot name="header">
        <x-tr-header>
            <x-th>Name</x-th>
            <x-th>Provider</x-th>
            {{-- Badges, like "active" (has active servers) --}}
            <x-th></x-th>
            {{-- Buttons, like "remove" and "edit" (Edit name and token, like in Forge.) --}}
            <x-th></x-th>
        </x-tr-header>
    </x-slot>

    <x-slot name="body">
        {{-- TODO: Check how it looks with longer names. --}}
        @foreach($providers as $provider)
            <x-tr>
                <x-td>{{ $provider->name }}</x-td>
                <x-td>{{ __('account.providers.' . $provider->provider . '.name') }}</x-td>
                <x-td></x-td>
                <x-td></x-td>
            </x-tr>
        @endforeach
    </x-slot>

</x-table-section>
