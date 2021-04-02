<x-page-content>

    <x-slot name="menu">
        @include('account._menu')
    </x-slot>

    @include($this->page)

</x-page-content>
