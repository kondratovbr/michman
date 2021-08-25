{{--TODO: Can the interaction here be made nicer? Like some transition or an animation. Check out how Forge does.--}}

<x-sub-page name="deployment">

    @if(! $project->repoInstalled())
        <livewire:projects.install-repo-form :project="$project" />
    @else

        <livewire:deployments.deployments-index-table :project="$project" />

        <x-section-separator/>

        <div>Deployment Trigger URL</div>

        <x-section-separator/>

        <livewire:projects.project-environment-edit-form :project="$project" />

        <x-section-separator/>

        <livewire:projects.project-deploy-script-edit-form :project="$project" />

        <x-section-separator/>

        <livewire:projects.project-gunicorn-config-edit-form :project="$project" />

        <x-section-separator/>

        <livewire:projects.project-nginx-config-edit-form :project="$project" />

        <x-section-separator/>

        <div>Maintenance Mode</div>

        <x-section-separator/>

        <div>Deployment Branch</div>

        <x-section-separator/>

        <div>Update Git Remote</div>

        <x-section-separator/>

        <livewire:projects.uninstall-repo-form :project="$project" />

    @endif

</x-sub-page>
