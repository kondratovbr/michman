{{--TODO: Figure out how to put small modals (like this one) on the middle of the screen, when the screen allows for it.--}}

<x-modals.form wireModel="modalOpened" modalId="logoutSessionsModal">

    <x-slot name="header">
        <h3 class="text-lg font-medium">
        {{ __('account.profile.sessions.logout') }}
        </h3>
    </x-slot>

    <x-slot name="content">
        <div class="max-w-prose">
            {{ __('account.profile.sessions.enter_password') }}

            <x-field
                x-data="{}"
                class="mt-4"
                {{-- When modal opens - Livewire fires a browser event "confirming-logout-sessions", which we use here to autofocus the password field using x-ref on it. --}}
                {{-- I don't know why it doesn't work without timeout regardless of where I put this line. --}}
                x-on:confirming-logout-sessions.window="setTimeout(() => $refs.password.focus(), 50)"
            >
                <x-label>{{ __('forms.password.label') }}</x-label>
                <x-inputs.password
                    class="max-w-md"
                    name="password"
                    id="password_modal"
                    x-ref="password"
                    wire:model.defer="password"
                    required
                />
{{--                TODO: IMPORTANT! Verify that it even works. Don't forget that this needs improvement.--}}
                <x-input-error for="password" class="mt-2" />
            </x-field>

        </div>

    </x-slot>

    <x-slot name="actions">
        <x-buttons>
            <x-buttons.primary
                wire:click.prevent="logoutOtherSessions"
                wire:loading.attr="disabled"
            >
                {{ __('account.profile.sessions.logout') }}
            </x-buttons.primary>
            <x-buttons.secondary
                {{-- "show" Alpine variable is @entangled with the Livewire model. See above. --}}
                {{-- Triggering the Alpines one first ensures no delay between a button press and the modal closing. --}}
                x-on:click.prevent="show = false"
                wire:loading.attr="disabled"
            >
                {{ __('buttons.cancel') }}
            </x-buttons.secondary>
        </x-buttons>
    </x-slot>

</x-modals.form>
