{{--TODO: Can the interaction here be made nicer? Like some transition or an animation. Check out how Forge does.--}}

@if($project->removingRepo)
    <livewire:projects.removing-repo-placeholder :project="$project" />
@elseif(! $project->repoInstalled())
    <livewire:projects.install-repo-form :project="$project" />
@else

    <livewire:deployments.deployments-index-table :project="$project" />

    <x-section-separator/>

    <livewire:projects.quick-deploy-form :project="$project" />

    <x-section-separator/>

    <livewire:projects.project-deployment-branch-edit-form :project="$project" />

    <x-section-separator/>

    <livewire:projects.uninstall-repo-form :project="$project" />

@endif
