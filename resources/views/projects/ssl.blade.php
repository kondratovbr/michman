<x-sub-page name="ssl">

    <livewire:certificates.create-lets-encrypt-certificate-form :project="$project" />

    <x-section-separator/>

    <livewire:certificates.certificates-index-table :project="$project" />

</x-sub-page>
