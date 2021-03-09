<x-layouts.guest>
    <x-auth-box>

        <x-jet-validation-errors class="mb-4" />

        <x-form method="POST" action="{{ route('register') }}">

            <x-field>
                <x-label for="email" value="{{ __('Email') }}" />
                <x-inputs.email
                    name="email"
                    required
                    autofocus
                />
            </x-field>

            <x-field>
                <x-label for="password" value="{{ __('Password') }}" />
                <x-inputs.password
                    name="password"
                    autocomplete="new-password"
                    required
                />
            </x-field>

            <x-field>
                <x-label for="password_confirmation" value="{{ __('Confirm Password') }}" />
                <x-inputs.password
                    name="password_confirmation"
                    autocomplete="new-password"
                    required
                />
            </x-field>

            @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                <div>
                    <x-label for="terms">
                        <div class="flex items-center">
                            <x-checkbox name="terms" id="terms" />

                            <div class="ml-2">
                                {!! __('I agree to the :terms_of_service and :privacy_policy', [
                                        'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'" class="underline text-sm text-gray-400 hover:text-gray-100">'.__('Terms of Service').'</a>',
                                        'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'" class="underline text-sm text-gray-400 hover:text-gray-100">'.__('Privacy Policy').'</a>',
                                ]) !!}
                            </div>
                        </div>
                    </x-label>
                </div>
            @endif

            <div class="flex items-center justify-end">
                <a class="underline text-sm text-gray-400 hover:text-gray-100" href="{{ route('login') }}">
                    {{ __('Already registered?') }}
                </a>

                <x-button class="ml-4">
                    {{ __('Register') }}
                </x-button>
            </div>

        </x-form>

    </x-auth-box>
</x-layouts.guest>
