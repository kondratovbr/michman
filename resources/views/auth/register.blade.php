{{--TODO: IMPORTANT! Needs OAuth options as well.--}}

<x-layouts.guest>
    <x-auth-box>

        <x-validation-errors class="mb-4" />

        <x-forms.vertical method="POST" action="{{ route('register') }}">

            <x-field>
                <x-label for="email" value="{{ __('Email') }}" />
                <x-inputs.email
                    name="email"
                    required
                    autofocus
                />
                <x-input-error for="email" />
            </x-field>

            <x-field>
                <x-label for="password" value="{{ __('Password') }}" />
                <x-inputs.password
                    name="password"
                    autocomplete="new-password"
                    required
                />
                <x-input-error for="password" />
            </x-field>

            <x-field>
                <x-label for="password_confirmation" value="{{ __('Confirm Password') }}">{{ __() }}</x-label>
                <x-inputs.password
                    name="password_confirmation"
                    autocomplete="new-password"
                    required
                />
                <x-input-error for="password_confirmation" />
            </x-field>

            @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                <x-field>
                    <x-checkbox-new
                        name="terms"
                        defaultState="on"
                        labelClass="text-sm"
                    >
                        <x-lang key="i-agree-terms-service"/>
                    </x-checkbox-new>
                    <x-input-error for="terms" />
                </x-field>
            @endif

            <div class="flex items-center justify-end">
                <a class="underline text-gray-400 hover:text-gray-100" href="{{ route('login') }}">
                    {{ __('auth.already-registered') }}
                </a>

                <x-button class="ml-4">
                    {{ __('buttons.register') }}
                </x-button>
            </div>

        </x-forms.vertical>

    </x-auth-box>
</x-layouts.guest>
