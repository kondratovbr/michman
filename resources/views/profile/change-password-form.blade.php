{{--TODO: IMPORTANT! Unfinished!--}}

<x-form-section submit="updatePassword">
    <x-slot name="title">
        {{ __('account.profile.password.title') }}
    </x-slot>

    <x-slot name="description">
        {{ __('account.profile.password.description') }}
    </x-slot>

    <x-slot name="form">
        <div class="space-y-4">

            <x-field>
                <x-label for="current_password">{{ __('forms.current-password.label') }}</x-label>
                <x-inputs.password
                    name="current_password"
                    wire:model.defer="current_password"
                />
                <x-input-error for="current_password" />
            </x-field>

            <x-field>
                <x-label for="password">{{ __('forms.new-password.label') }}</x-label>
                <x-inputs.password
                    name="password"
                    wire:model.defer="password"
                    autocomplete="new-password"
                />
                <x-input-error for="password" />
            </x-field>

            <x-field>
                <x-label for="password_confirmation">{{ __('forms.password_confirmation.label') }}</x-label>
                <x-inputs.password
                    name="password_confirmation"
                    errorName="password"
                    wire:model.defer="password_confirmation"
                    autocomplete="new-password"
                />
                <x-input-error for="password" />
            </x-field>

        </div>
    </x-slot>

    <x-slot name="actions">
        <div class="flex items-center space-x-3">
            <x-buttons.primary>
                {{ __('buttons.save') }}
            </x-buttons.primary>
            <x-action-message on="saved">
                {{ __('misc.saved') }}
            </x-action-message>
        </div>
    </x-slot>
</x-form-section>
