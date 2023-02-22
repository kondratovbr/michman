@can('create', [App\Models\Project::class, $server])
    <livewire:projects.create-project-form :server="$server" />
@else
    <x-projects.upgrade-subscription/>
@endcan

<x-section-separator/>

<livewire:projects.projects-index-table :server="$server" />
