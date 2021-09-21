{{--TODO: CRITICAL! Unfinished. Make nicer, add info, add progress indicator/spinner.--}}

<x-action-section>
    <x-slot name="title">{{ __('projects.quick-deploy.title') }}</x-slot>

    <x-slot name="content">

        <x-buttons>
            @if(is_null($this->hook) || $this->hook->isEnabling())
                <x-buttons.primary
                    wire:click.prevent="enable"
                    wire:loading.attr="disabled"
                    :loading="$this->hook?->isEnabling()"
                >{{ __('projects.quick-deploy.enable') }}</x-buttons.primary>
            @else
                <x-buttons.danger
                    wire:click.prevent="disable"
                    wire:loading.attr="disabled"
                    :loading="$this->hook->isDeleting()"
                >{{ __('projects.quick-deploy.disable') }}</x-buttons.danger>
            @endif

            <x-buttons.secondary
                wire:click.prevent="$refresh"
                wire:loading.attr="disabled"
            >Refresh</x-buttons.secondary>
        </x-buttons>

    </x-slot>
</x-action-section>
