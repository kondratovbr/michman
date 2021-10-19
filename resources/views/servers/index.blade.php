{{--TODO: IMPORTANT! Improve the single column layout - looks weird.--}}

{{--TODO: CRITICAL! The servers page should have notifications as well.--}}

<x-layouts.app-one-column>

    <x-slot name="header">
        <x-page-title>Servers</x-page-title>
    </x-slot>

    <livewire:servers.create-server-form/>

    <x-section-separator/>

    <livewire:servers.servers-index-table/>

</x-layouts.app-one-column>
