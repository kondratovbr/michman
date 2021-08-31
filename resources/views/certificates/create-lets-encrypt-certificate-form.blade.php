<x-form-section submit="store">

    <x-slot name="title">{{ __('projects.ssl.lets-encrypt.title') }}</x-slot>

    <x-slot name="description">{{ __('projects.ssl.lets-encrypt.description') }}</x-slot>

    <x-slot name="form">
        <div class="space-y-6">

            <x-message>{{ __('projects.ssl.lets-encrypt.explanation') }}</x-message>

            <x-field class="max-w-sm">
                <x-label>{{ __('projects.ssl.lets-encrypt.domains.title') }}</x-label>
                <x-inputs.text
                    name="domains"
                    wire:model.defer="domains"
                />
                <x-input-error for="domains" />
            </x-field>

        </div>
    </x-slot>

    <x-slot name="actions">
        <x-buttons.primary
            wire:click.prevent="store"
            wire:loading.attr="disabled"
        >{{ __('projects.ssl.lets-encrypt.button') }}</x-buttons.primary>
    </x-slot>

</x-form-section>
