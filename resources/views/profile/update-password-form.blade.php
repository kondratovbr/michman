{{--TODO: IMPORTANT! Unfinished!--}}

<x-form-section submit="updatePassword">
    <x-slot name="title">
        {{ __('Update Password') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Ensure your account is using a long, random password to stay secure.') }}
    </x-slot>

    <x-slot name="form">
        <div class="space-y-4">

            <x-field>
                <x-label for="current_password" value="{{ __('Current Password') }}" />
                <x-inputs.password
                    name="current_password"
                    wire:model.defer="state.current_password"
                />
                <x-jet-input-error for="current_password" class="mt-2" />
            </x-field>

            <x-field>
                <x-label for="password" value="{{ __('New Password') }}" />
                <x-inputs.password
                    name="password"
                    wire:model.defer="state.password"
                    autocomplete="new-password"
                />
                <x-jet-input-error for="password" class="mt-2" />
            </x-field>

            <x-field>
                <x-label for="password_confirmation" value="{{ __('Confirm Password') }}" />
                <x-inputs.password
                    name="password_confirmation"
                    wire:model.defer="state.password_confirmation"
                    autocomplete="new-password"
                />
                <x-jet-input-error for="password_confirmation" class="mt-2" />
            </x-field>

        </div>
    </x-slot>

    <x-slot name="actions">
        <x-buttons.primary>
            {{ __('Save') }}
        </x-buttons.primary>
        <x-jet-action-message class="ml-3" on="saved">
            {{ __('Saved.') }}
        </x-jet-action-message>
    </x-slot>
</x-form-section>
