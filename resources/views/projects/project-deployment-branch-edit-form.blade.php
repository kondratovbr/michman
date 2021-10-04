<x-form-section submit="update">

    <x-slot name="title">{{ __('projects.branch.title') }}</x-slot>

    <x-slot name="form">
        <x-field class="max-w-sm">
            <x-label>{{ __('projects.branch.label') }}</x-label>
            <x-inputs.text
                name="branch"
                wire:model="branch"
            />
            <x-input-error for="branch" />
        </x-field>
    </x-slot>

    <x-slot name="actions">
        <x-buttons.primary
            wire:click.prevent="update"
            wire:loading.attr="disabled"
        >{{ __('projects.branch.button') }}</x-buttons.primary>
    </x-slot>

</x-form-section>
