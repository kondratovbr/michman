{{--TODO: CRITICAL! Add some explanation and a link to docs (with more detailed explanation).--}}
{{--TODO: There's a weird margin on the top of the box here. Should fix.--}}

<x-form-section submit="store">

    <x-slot name="title">{{ __('projects.deployment.nginx-config.title') }}</x-slot>

    <x-slot name="form">
        <x-field class="mt-6">
            <x-editor
                wire:model="nginxConfig"
{{--                mode="sh"--}}
            >{{ $nginxConfig }}</x-editor>
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
