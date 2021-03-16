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
                    wire:model.defer="password"
                    required
                />
{{--                TODO: IMPORTANT! Verify that it even works. Don't forget that this needs improvement.--}}
                <x-jet-input-error for="password" class="mt-2" />
            </x-field>

        </div>

        <div>Foo: {{ $this->foo }}</div>

    </x-slot>

    <x-slot name="actions">
        <x-button
            wire:click.prevent="logoutOtherSessions"
            wire:loading.attr="disabled"
        >
            {{ __('account.profile.sessions.logout') }}
        </x-button>
{{--        TODO: IMPORTANT! Re-do and restyle this button. All other places with these secondary buttons as well. Also, create a "buttons" container.--}}
        <x-jet-secondary-button
            class="ml-2"
            {{-- "show" Alpine variable is @entangled with the Livewire model. See above. --}}
            {{-- Triggering the Alpines one first ensures no delay between a button press and the modal closing. --}}
            x-on:click.prevent="show = false"
            wire:loading.attr="disabled"
        >
            {{ __('buttons.cancel') }}
        </x-jet-secondary-button>
    </x-slot>

</x-modals.form>
