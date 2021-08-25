{{--TODO: CRITICAL! Add some more explanation or a link to docs (with more detailed explanation).--}}

<x-form-section submit="store">

    <x-slot name="title">{{ __('projects.config.deploy-script.title') }}</x-slot>

    <x-slot name="form">

        <x-message>{{ __('projects.config.deploy-script.explanation') }}</x-message>

        <x-field class="mt-6">
            <x-editor
                wire:model="script"
                mode="sh"
            >{{ $script }}</x-editor>
        </x-field>
    </x-slot>

    <x-slot name="actions">
        <div class="flex items-center space-x-3">
            <x-buttons.secondary
                wire:click.prevent="reload"
                wire:loading.attr="disabled"
            >{{ __('buttons.reload') }}</x-buttons.secondary>
            <x-buttons.primary
                wire:click.prevent="update"
                wire:loading.attr="disabled"
            >{{ __('buttons.save') }}</x-buttons.primary>
        </div>
    </x-slot>

</x-form-section>
