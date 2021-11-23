{{--TODO: This modal needs an "X" button in the corner, like Forge does. Just in case.--}}

<x-modals.dialog wire:model="modalOpen" modalId="viewDeploymentLogModal">

    <x-slot name="header">
        <h3>
            {{ __('deployments.log-modal-title') }}
            <strong>{{ $server->name }}</strong>
        </h3>
    </x-slot>

    <x-slot name="content">
        <x-logs :logs="$logs" />
    </x-slot>

    <x-slot name="actions">
        <x-buttons.secondary
            x-on:click.stop="$dispatch('close-modal')"
            wire:loading.attr="disabled"
        >
            {{ __('buttons.close') }}
        </x-buttons.secondary>
    </x-slot>

</x-modals.dialog>
