<x-sub-page name="queue">

    <livewire:workers.create-worker-form :project="$project" />

    <x-section-separator/>

    <livewire:workers.workers-index-table :project="$project" />

</x-sub-page>
