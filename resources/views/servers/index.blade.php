{{--TODO: IMPORTANT! Improve the single column layout - looks weird.--}}

<x-layouts.app-one-column>

    <x-slot name="notifications">
        <livewire:notifications/>
    </x-slot>

    <x-slot name="header">
        <x-page-title>{{ __('servers.title') }}</x-page-title>
    </x-slot>

    <div class="space-y-10 sm:space-y-0">

        @can('create', App\Models\Server::class)
            <livewire:servers.create-server-form/>
        @else
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
        @endcan

        <x-section-separator/>

        <livewire:servers.servers-index-table/>

    </div>

</x-layouts.app-one-column>
