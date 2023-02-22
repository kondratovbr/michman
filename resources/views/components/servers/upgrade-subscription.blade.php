<x-action-section>
    <x-slot name="title">
        {{ __('servers.create.title') }}
    </x-slot>

    <x-slot name="content">
        <x-lang key="servers.server-limit-reached" />

        <x-buttons.primary
            class="mt-5"
            :link="true"
            href="/billing"
        >{{ __('billing.upgrade-button') }}</x-buttons.primary>
    </x-slot>
</x-action-section>
