{{--TODO: CRITICAL! Unfinished? Need loading animations, etc.--}}

<x-form-section submit="store">

    <x-slot name="title">{{ __('projects.create.title') }}</x-slot>

    <x-slot name="form">
        <div class="space-y-6">

            <x-field class="max-w-sm">
                <x-label>{{ __('projects.create.form.domain.label') }}</x-label>
                <x-inputs.text
                    name="domain"
                    wire:model.defer="domain"
                />
                <x-input-error for="domain" />
            </x-field>

        </div>
    </x-slot>

    <x-slot name="actions">
        <x-buttons.primary
            wire:click.prevent="store"
            wire:loading.attr="disabled"
        >
            {{ __('projects.create.form.button') }}
        </x-buttons.primary>
    </x-slot>

</x-form-section>
