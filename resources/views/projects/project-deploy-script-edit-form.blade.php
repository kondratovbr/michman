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
            <x-buttons.primary
                wire:click.prevent="update"
                wire:loading.attr="disabled"
            >{{ __('buttons.save') }}</x-buttons.primary>
            <x-buttons.secondary
                wire:click.prevent="rollback"
                wire:loading.attr="disabled"
                :disabled="! $this->modified"
            >{{ __('buttons.rollback') }}</x-buttons.secondary>
            @if($this->modified)
                <x-badge>Modified</x-badge>
            @endif
        </div>
    </x-slot>

</x-form-section>
