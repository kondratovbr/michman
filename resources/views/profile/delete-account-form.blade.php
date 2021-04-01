{{--TODO: IMPORTANT! Unfinished!--}}

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

{{--        TODO: Actually implement the feature. It isn't that simple.--}}
        {{-- Temorary Sorry Modal --}}
        <x-modals.dialog wireModel="confirmingUserDeletion">
            <x-slot name="header">
                {{ __('account.profile.delete.sorry.title') }}
            </x-slot>

            <x-slot name="content">
                <div class="space-y-4">
                    <p>{{ __('account.profile.delete.sorry.content') }}</p>
                    <p>{{ __('account.profile.delete.sorry.contact-support') }}</p>
                </div>
            </x-slot>

            <x-slot name="actions">
                <x-buttons>
{{--                    TODO: IMPORTANT! Implement tech support and this button.--}}
                    <x-buttons.primary>
                        {{ __('account.profile.delete.sorry.contact-button') }}
                    </x-buttons.primary>
                    <x-buttons.secondary
                        wire:click="$toggle('confirmingUserDeletion')"
                        wire:loading.attr="disabled"
                    >
                        {{ __('buttons.cancel') }}
                    </x-buttons.secondary>
                </x-buttons>
            </x-slot>
        </x-modals.dialog>

        {{-- Actual Delete User Confirmation Modal --}}
        {{--
        <x-modals.dialog wireModel="confirmingUserDeletion">
            <x-slot name="header">
                {{ __('account.profile.delete.modal-title') }}
            </x-slot>

            <x-slot name="content">
                {{ __('account.profile.delete.are-you-sure') }}

                <div
                    class="mt-4"
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
            </x-slot>

            <x-slot name="actions">
                <x-buttons>
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
                </x-buttons>
            </x-slot>
        </x-modals.dialog>
        --}}

    </x-slot>
</x-action-section>
