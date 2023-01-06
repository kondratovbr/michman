<x-layouts.guest>
    <x-auth-box>

        <x-slot name="title">
            {{ __('auth.register-on', ['app' => siteName()]) }}
        </x-slot>

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
                <x-buttons.primary class="ml-4">
                    {{ __('buttons.register') }}
                </x-buttons.primary>
            </div>

        </x-forms.vertical>

        <x-hr>{{ __('misc.or') }}</x-hr>

        <div class="flex flex-col items-center">
            <h3>{{ __('auth.register-via') }}</h3>
            <x-oauth-buttons class="mt-2"/>
        </div>

        <x-slot name="bottomMessage">
            <div class="flex justify-center space-x-2">
                <p>{{ __('auth.already-registered') }}</p>
                <x-link href="{{ route('login') }}">{{ __('auth.login') }}</x-link>
            </div>
        </x-slot>

    </x-auth-box>

    @push('scripts')
        {{-- Reddit pixel event --}}
        <script>
            rdt('track', 'ViewContent');
        </script>
    @endpush
</x-layouts.guest>
