<x-dialog-modal wire:model="confirmingLogout">

    <x-slot name="title">
        {{ __('account.profile.sessions.logout') }}
    </x-slot>

    <x-slot name="content">
        {{ __('account.profile.sessions.enter_password') }}

        <div
            class="mt-4"
            x-data="{}"
{{--            TODO: IMPORTANT! WTF is this?--}}
            x-on:confirming-logout-other-browser-sessions.window="setTimeout(() => $refs.password.focus(), 250)"
        >
            <x-jet-input
                type="password"
                class="mt-1 block w-3/4"
                placeholder="{{ __('Password') }}"
                x-ref="password"
                wire:model.defer="password"
                wire:keydown.enter="logoutOtherBrowserSessions"
            />
            <x-jet-input-error for="password" class="mt-2" />
        </div>
    </x-slot>

    <x-slot name="footer">
        <x-jet-secondary-button
            wire:click="$toggle('confirmingLogout')"
            wire:loading.attr="disabled"
        >
            {{ __('buttons.cancel') }}
        </x-jet-secondary-button>

        <x-button
            class="ml-2"
            wire:click="logoutOtherBrowserSessions"
            wire:loading.attr="disabled"
        >
            {{ __('account.profile.sessions.logout') }}
        </x-button>
    </x-slot>

</x-dialog-modal>
