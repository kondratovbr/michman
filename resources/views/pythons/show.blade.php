<x-sub-page name="pythons">

    <livewire:pythons.update-python-config-form :server="$server" />

    <x-section-separator/>

    <livewire:pythons.pythons-index-table :server="$server" />

</x-sub-page>
