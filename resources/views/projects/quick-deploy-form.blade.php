{{--TODO: CRITICAL! Unfinished. Make nicer, add info, add progress indicator/spinner.--}}

<x-action-section>
    <x-slot name="title">{{ __('projects.quick-deploy.title') }}</x-slot>

    <x-slot name="content">

        @if(is_null($hook) || $hook->isEnabling())
            <x-buttons.primary
                wire:click.prevent="enable"
                wire:loading.attr="disabled"
                :loading="$hook?->isEnabling()"
            >{{ __('projects.quick-deploy.enable') }}</x-buttons.primary>
        @else
            <x-buttons.danger
                wire:click.prevent="disable"
                wire:loading.attr="disabled"
                :loading="$hook->isDeleting()"
            >{{ __('projects.quick-deploy.disable') }}</x-buttons.danger>
        @endif

    </x-slot>
</x-action-section>