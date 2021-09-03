{{--TODO: CRITICAL! CONTINUE. Figure out how to run (or how people usually run) queues in Django and implement the thing here.--}}

<x-sub-page name="queue">

    <livewire:workers.create-worker-form :project="$project" />

    <x-section-separator/>

    <livewire:workers.workers-index-table :project="$project" />

</x-sub-page>
