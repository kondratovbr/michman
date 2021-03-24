{{--TODO: IMPORTANT! Needs OAuth options as well.--}}

<x-layouts.guest>
    <x-auth-box>

        <x-validation-errors class="mb-4" />

        <x-forms.vertical method="POST" action="{{ route('register') }}">

            <x-field>
                <x-label for="email">{{ __('forms.email.label') }}</x-label>
                <x-inputs.email
                    name="email"
                    required
                    autofocus
                />
                <x-input-error for="email" />
            </x-field>

            <x-field>
                <x-label for="password">{{ __('forms.password.label') }}</x-label>
                <x-inputs.password
                    name="password"
                    autocomplete="new-password"
                    required
                />
                <x-input-error for="password" />
            </x-field>

            <x-field>
                <x-label for="password_confirmation">{{ __('forms.password_confirmation.label') }}</x-label>
                <x-inputs.password
                    name="password_confirmation"
                    errorName="password"
                    autocomplete="new-password"
                    required
                />
                <x-input-error for="password" />
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
                <x-link href="{{ route('login') }}">{{ __('auth.already-registered') }}</x-link>
                <x-button class="ml-4">
                    {{ __('buttons.register') }}
                </x-button>
            </div>

        </x-forms.vertical>

    </x-auth-box>
</x-layouts.guest>
