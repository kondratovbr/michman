{{--TODO: CRITICAL! Unfinished. Make nicer, add info, add progress indicator/spinner.--}}

<x-action-section>
    <x-slot name="title">{{ __('projects.quick-deploy.title') }}</x-slot>

    <x-slot name="content">

        <x-buttons.primary
            wire:click.prevent="enable"
        >{{ __('projects.quick-deploy.enable') }}</x-buttons.primary>

        <x-buttons.danger
            wire:click.prevent="disable"
        >{{ __('projects.quick-deploy.disable') }}</x-buttons.danger>

    </x-slot>
</x-action-section>
