<x-action-section>
    <x-slot name="title">
        {{ __('servers.create.title') }}
    </x-slot>

    <x-slot name="content">
        <x-lang key="servers.add-provider" />

        <x-buttons.primary
            class="mt-5"
            :link="true"
            href="{{ route('account.show', 'providers') }}"
        >Add a new server provider</x-buttons.primary>

    </x-slot>
</x-action-section>
