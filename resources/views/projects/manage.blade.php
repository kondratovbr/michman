<x-action-section>
    <x-slot name="title">
        {{ __('projects.manage.deploy-key.title') }}
    </x-slot>

    <x-slot name="content">
        <x-message>{{ __('projects.manage.deploy-key.info') }}</x-message>
        <x-copy-code-block class="mt-6" :wrap="true">
            {!! $project->deploySshKey->publicKeyString !!}
        </x-copy-code-block>
    </x-slot>
</x-action-section>

<x-section-separator/>

<livewire:projects.delete-project-form :project="$project" />
