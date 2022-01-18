{{--TODO: CRITICAL! Add some explanation and a link to docs (with more detailed explanation). NOTE: I changed this config to be not a whole "server" block, but only a customizable part of it. Explain it in docs and maybe provide a full config in there as well.--}}
{{--TODO: There's a weird margin on the top of the box here. Should get fixed with an explanation block..--}}

<x-form-section submit="store">

    <x-slot name="title">{{ __('projects.config.nginx-config.title') }}</x-slot>

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
                <x-badge>{{ __('misc.modified') }}</x-badge>
            @endif
        </div>
    </x-slot>

</x-form-section>
