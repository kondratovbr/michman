<x-sub-page name="projects">

    <livewire:projects.create-project-form :server="$server" />

    <x-section-separator/>

    <livewire:projects.projects-index-table :server="$server" />

    <x-section-separator/>

</x-sub-page>
