<x-modals.dialog wireModel="modalOpen" modalId="viewDeploymentLogModal">

    <x-slot name="header">
        <h3>Deployment Log From XXX server</h3>
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
