<x-action-section>
    <x-slot name="title">
        {{ __('account.profile.delete.title') }}
    </x-slot>

    <x-slot name="description">
        {{ __('account.profile.delete.description') }}
    </x-slot>

    <x-slot name="content">
        <div class="max-w-prose text-sm">
            {{ __('account.profile.delete.explanation') }}
        </div>

        <div class="mt-5">
            <x-buttons.danger
                wire:click="confirmUserDeletion"
                wire:loading.attr="disabled"
            >
                {{ __('account.profile.delete.delete-button') }}
            </x-buttons.danger>
        </div>

        <x-modals.dialog wire:model="confirmingUserDeletion">
            <x-slot name="header">
                {{ __('account.profile.delete.modal-title') }}
            </x-slot>

            <x-slot name="content">
                <div class="space-y-4">
                    <p>{{ __('account.profile.delete.explanation') }}</p>

                    <p>{{ __('account.profile.delete.enter-password') }}</p>

                    <div
                        x-data="{}"
                        x-on:confirming-delete-user.window="setTimeout(() => $refs.password.focus(), 250)"
                    >
                        <x-label>{{ __('forms.password.label') }}</x-label>
                        <x-inputs.password
                            name="password"
                            x-ref="password"
                            wire:model.defer="password"
                            wire:keydown.enter="deleteUser"
                        />
                        <x-input-error for="password" />
                    </div>
                </div>
            </x-slot>

            <x-slot name="actions">
                <div class="flex justify-between">
                    <x-buttons.secondary
                        wire:click="$toggle('confirmingUserDeletion')"
                        wire:loading.attr="disabled"
                    >
                        {{ __('buttons.cancel') }}
                    </x-buttons.secondary>

                    <x-buttons.danger
                        wire:click="deleteUser"
                        wire:loading.attr="disabled"
                    >
                        {{ __('account.profile.delete.delete-button') }}
                    </x-buttons.danger>
                </div>
            </x-slot>
        </x-modals.dialog>

    </x-slot>
</x-action-section>
