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
            <x-servers.upgrade-subscription/>
        @endcan

        <x-section-separator/>

        <livewire:servers.servers-index-table/>

    </div>

</x-layouts.app-one-column>
