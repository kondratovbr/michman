{{--TODO: CRITICAL! Add a status bar on top or something like that - similar to Forge.--}}

<x-slot name="header">
    <x-page-title>
        {{ $project->domain }}
    </x-page-title>
</x-slot>

<x-page-content>

    <x-slot name="menu">
        @include('projects._menu')
    </x-slot>

    <x-slot name="notifications">
        <livewire:notifications/>
    </x-slot>

    <x-sub-page wire:key="{{ $this->show }}" name="{{ $this->show }}">
        @include($this->page)
    </x-sub-page>

</x-page-content>
