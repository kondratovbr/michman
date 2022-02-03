<x-action-section>
    <x-slot name="title">
        {{ __('servers.create.title') }}
    </x-slot>

    <x-slot name="content">
        @if(user()->subscribed())
            <x-lang key="servers.server-limit-reached" />
        @else
            <x-lang key="servers.not-subscribed" />
        @endif

        <x-buttons.primary
            class="mt-5"
            :link="true"
            href="/billing"
        >{{ __('billing.upgrade-button') }}</x-buttons.primary>
    </x-slot>
</x-action-section>
