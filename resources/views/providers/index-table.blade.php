{{--TODO: IMPORTANT! Add badges and buttons, implement removing providers. Note that stackable buttons shouldn't be very rounded.--}}
{{--TODO: Figure out how to verify that API is still accessible by a credentials and highlight/notify when it isn't.--}}
{{--TODO: Figure out how to animate the action of storing a new provider. davidkpiano/flipping ? is it computationally intensive?--}}

<x-table-section>
    <x-slot name="title">{{ __('account.providers.table.title') }}</x-slot>

    <x-slot name="header">
        <x-tr-header>
            <x-th>{{ __('account.providers.table.name') }}</x-th>
            <x-th>{{ __('account.providers.table.provider') }}</x-th>
            {{-- Badges, like "active" (has active servers) or "ready" (no servers, but ready to rock-n-roll) and "error" (no access, for some reason) --}}
            <x-th></x-th>
            {{-- Buttons, like "remove" and "edit" (Edit name and token, like in Forge.) --}}
{{--            TODO: When implementing the edit modal don't forget to put a big warning that the key should belong to the same account or existing servers may break.--}}
            <x-th></x-th>
        </x-tr-header>
    </x-slot>

    <x-slot name="body">

        {{-- TODO: Check how it looks with longer names. --}}
        @foreach($providers as $provider)
            <x-tr>
                <x-td>{{ $provider->name }}</x-td>
                <x-td>{{ __('account.providers.' . $provider->provider . '.name') }}</x-td>
                <x-td><x-providers.badge :provider="$provider" /></x-td>
                <x-td></x-td>
            </x-tr>
        @endforeach
    </x-slot>

</x-table-section>
