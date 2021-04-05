<x-slot name="header">
    <x-page-title>
        {{ __('nav.account') }}
    </x-page-title>
</x-slot>

<x-page-content>

    <x-slot name="menu">
        @include('account._menu')
    </x-slot>

    <x-sub-page wire:key="{{ $this->show }}">
        @include($this->page)
    </x-sub-page>

</x-page-content>
