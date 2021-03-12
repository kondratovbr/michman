{{--TODO: IMPORTANT! Unfinished!--}}

<x-layouts.guest>
    <x-auth-box>

        <div class="mb-4 text-sm text-gray-600">
            {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
        </div>

        <x-jet-validation-errors class="mb-4" />

        <x-forms.vertical method="POST" action="{{ route('password.confirm') }}">

            <x-field>
                <x-label for="password" value="{{ __('Password') }}" />
                <x-inputs.password
                    name="password"
                    required
                    autofocus
                />
            </x-field>

            <div class="flex justify-end mt-4">
                <x-button class="ml-4">
                    {{ __('Confirm') }}
                </x-button>
            </div>

        </x-forms.vertical>

    </x-auth-box>
</x-layouts.guest>
