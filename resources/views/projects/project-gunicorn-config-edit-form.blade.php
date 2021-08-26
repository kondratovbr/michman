{{--TODO: CRITICAL! Add some explanation and a link to docs (with more detailed explanation).--}}
{{--TODO: There's a weird margin on the top of the box here. Should fix.--}}

<x-form-section submit="store">

    <x-slot name="title">{{ __('projects.config.gunicorn-config.title') }}</x-slot>

    <x-slot name="form">
        <x-field class="mt-6">
            <x-editor
                wire:model="gunicornConfig"
{{--                mode="sh"--}}
            >{{ $gunicornConfig }}</x-editor>
        </x-field>
    </x-slot>

    <x-slot name="actions">
        <div class="flex items-center space-x-3">
            @if($this->modified)
                <x-badge>Modified</x-badge>
            @endif
            <x-buttons.secondary
                wire:click.prevent="rollback"
                wire:loading.attr="disabled"
                :disabled="! $this->modified"
            >{{ __('buttons.rollback') }}</x-buttons.secondary>
            <x-buttons.primary
                wire:click.prevent="update"
                wire:loading.attr="disabled"
            >{{ __('buttons.save') }}</x-buttons.primary>
        </div>
    </x-slot>

</x-form-section>
