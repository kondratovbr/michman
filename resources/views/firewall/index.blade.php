<x-sub-page name="firewall">

    <livewire:firewall.firewall-create-form :server="$server" />

    <x-section-separator/>

    <livewire:firewall.firewall-index-table :server="$server" />

</x-sub-page>
