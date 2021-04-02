<x-layouts.app-with-menu>

    <x-slot name="header">
        <x-page-title>
            {{ __('nav.account') }}
        </x-page-title>
    </x-slot>

    <livewire:account-page :show="$show" />

</x-layouts.app-with-menu>
