{{-- TODO: IMPORTANT! Add an info block here explaining how config files work in relation to deployments.--}}

<livewire:projects.project-environment-edit-form :project="$project" />

<x-section-separator/>

<livewire:projects.project-deploy-script-edit-form :project="$project" />

<x-section-separator/>

<livewire:projects.project-gunicorn-config-edit-form :project="$project" />

<x-section-separator/>

<livewire:projects.project-nginx-config-edit-form :project="$project" />
