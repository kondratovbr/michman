{{--TODO: IMPORTANT! Needs a "Register" link/button.--}}
{{--TODO: IMPORTANT! Needs OAuth buttons as well.--}}
{{--TODO: Also need a title somewhere here. Like on GitHub: "Sing in to GitHub".--}}

<x-layouts.guest>
    <x-auth-box>

        <x-validation-errors class="mb-4" />

        @if (session('status'))
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ session('status') }}
            </div>
        @endif

        <x-forms.vertical method="POST" action="{{ route('login') }}">

            <x-field>
                <x-label for="email" value="{{ __('forms.email.label') }}" />
                <x-inputs.email
                    name="email"
                    required
                    autofocus
                    {{-- Laravel Fortify returns an error tied to "email" field on failed login attempt (for some reason),
                    which we don't want to show here - it will be showed on the top block instead. --}}
                    :showErrors="false"
                />
            </x-field>

            <x-field>
                <x-label for="password" value="{{ __('forms.password.label') }}" />
                <x-inputs.password
                    name="password"
                    required
                />
            </x-field>

            <x-field>
                <x-checkbox-new
                    name="remember"
                    defaultState="on"
                    labelClass="text-sm"
                >{{ __('auth.remember') }}</x-checkbox-new>
            </x-field>

            <div class="flex items-center justify-end">
                @if (Route::has('password.request'))
                    <a class="underline text-sm text-gray-400 hover:text-gray-100" href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif

                <x-buttons.primary class="ml-4">
                    {{ __('buttons.login') }}
                </x-buttons.primary>
            </div>

        </x-forms.vertical>

    </x-auth-box>

</x-layouts.guest>
