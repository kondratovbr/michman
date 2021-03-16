<x-modals.form wireModel="modalOpened">

    <x-slot name="header">
        {{ __('account.profile.sessions.logout') }}
    </x-slot>

    <x-slot name="content">
        <div class="max-w-prose">
            {{ __('account.profile.sessions.enter_password') }}

            <x-field>
                <x-label>{{ __('forms.password.label') }}</x-label>
                <x-inputs.password
                    name="password"
                    id="password_modal"
{{--                    x-ref="password"--}}
                    wire:model="password"
{{--                    wire:keydown.enter="logoutOtherBrowserSessions"--}}
                />
{{--                TODO: IMPORTANT! Verify that it even works. Don't forget that this needs improvement.--}}
                <x-jet-input-error for="password" class="mt-2" />
            </x-field>

        </div>
    </x-slot>

    <x-slot name="actions">
        <x-button
            wire:click="logoutOtherSessions"
            wire:loading.attr="disabled"
        >
            {{ __('account.profile.sessions.logout') }}
        </x-button>
{{--        TODO: IMPORTANT! Re-do and restyle this button. All other places with these secondary buttons as well.--}}
        <x-jet-secondary-button
            class="ml-2"
            wire:click="$toggle('modalOpened')"
            wire:loading.attr="disabled"
        >
            {{ __('buttons.cancel') }}
        </x-jet-secondary-button>
    </x-slot>

</x-modals.form>
