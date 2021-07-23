{{--TODO: Can the interaction here be made nicer? Like some transition or an animation. Check out how Forge does.--}}

<x-sub-page name="repo">

    @if(! $project->repoInstalled())
        <livewire:projects.install-repo-form :project="$project" />
    @else
        <div>Repo installed!</div>
    @endif

    <x-section-separator/>

    //

    <x-section-separator/>

    //

</x-sub-page>
