{{--TODO: CRITICAL! Add some explanation and a link to docs (with more detailed explanation).--}}

<x-form-section submit="store">

    <x-slot name="title">{{ __('projects.deployment.env.title') }}</x-slot>

    <x-slot name="form">
        <x-field>
            <x-editor
                wire:model="environment"
                mode="sh"
            >{{ $environment }}</x-editor>
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
