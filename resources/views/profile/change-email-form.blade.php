<x-form-section submit="changeEmail">

    <x-slot name="title">
        {{ __('account.profile.email.title') }}
    </x-slot>

    <x-slot name="description">
        {{ __('account.profile.email.description') }}
    </x-slot>

    <x-slot name="form">
        <x-field>
            <x-label for="email">{{ __('forms.email.label') }}</x-label>
            <x-inputs.email
                name="email"
                wire:model.defer="email"
                autocomplete="email"
            />
            <x-input-error for="email" />
        </x-field>
    </x-slot>

    <x-slot name="actions">
        <div class="flex items-center space-x-3">
            <x-buttons.primary
                wire:loading.attr="disabled"
                wire:target="photo"
            >
                {{ __('buttons.save') }}
            </x-buttons.primary>
            <x-action-message on="saved">
                {{ __('misc.saved') }}
            </x-action-message>
        </div>
    </x-slot>

</x-form-section>
