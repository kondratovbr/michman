<x-layouts.guest>
    <x-auth-box>

        <x-jet-validation-errors class="mb-4" />

        <x-form method="POST" action="{{ route('password.update') }}">

            <x-url-token/>

            <x-field>
                <x-label for="email" value="{{ __('Email') }}" />
                <x-inputs.email
                    name="email"
                    :value="old('email', $request->email)"
                    required
                    autofocus
                />
            </x-field>

            <x-field>
                <x-label for="password" value="{{ __('Password') }}" />
                <x-inputs.password
                    name="password"
                    required
                    autocomplete="new-password"
                />
            </x-field>

            <x-field>
                <x-label for="password_confirmation" value="{{ __('Confirm Password') }}" />
                <x-inputs.password
                    name="password_confirmation"
                    required
                    autocomplete="new-password"
                />
            </x-field>

            <div class="flex items-center justify-end mt-4">
                <x-button>
                    {{ __('Reset Password') }}
                </x-button>
            </div>

        </x-form>

    </x-auth-box>
</x-layouts.guest>
