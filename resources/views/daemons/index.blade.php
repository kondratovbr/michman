{{--TODO: CRITICAL! CONTINUE.--}}

<x-sub-page name="daemons">

    <livewire:daemons.create-daemon-form :server="$server" />

    <x-section-separator/>

    <livewire:daemons.daemons-index-table :server="$server" />

</x-sub-page>
