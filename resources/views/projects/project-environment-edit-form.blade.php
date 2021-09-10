{{--TODO: CRITICAL! Add some more explanation or a link to docs (with more detailed explanation).--}}

<x-form-section submit="store">

    <x-slot name="title">{{ __('projects.config.env.title') }}</x-slot>

    <x-slot name="form">

        <x-message><x-lang key="projects.env-help" /></x-message>

        <x-field class="mt-6">
            <x-editor
                wire:model="environment"
                mode="sh"
            >{{ $environment }}</x-editor>
        </x-field>
    </x-slot>

    <x-slot name="actions">
{{--        TODO: CRITICAL! Make these buttons spaced out (the rollback button is pretty dangerous), i.e. "justify-between". But for this I'll have to remove positioning in form-section and update every place I use it. Update other config editing forms as well.--}}
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
