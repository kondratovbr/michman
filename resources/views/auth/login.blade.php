{{--TODO: IMPORTANT! Needs a "Register" link/button.--}}
{{--TODO: IMPORTANT! Needs OAuth buttons as well.--}}

<x-layouts.guest>
    <x-auth-box>

        <x-jet-validation-errors class="mb-4" />

        @if (session('status'))
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ session('status') }}
            </div>
        @endif

        <x-forms.vertical method="POST" action="{{ route('login') }}">

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
                    required
                />
            </x-field>

            <x-field>
                <label for="remember_me" class="flex items-center">
                    <x-checkbox id="remember_me" name="remember" />
                    <span class="ml-2 text-sm">{{ __('Remember me') }}</span>
                </label>
            </x-field>

            <div class="flex items-center justify-end">
                @if (Route::has('password.request'))
                    <a class="underline text-sm text-gray-400 hover:text-gray-100" href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif

                <x-button class="ml-4">
                    {{ __('Log in') }}
                </x-button>
            </div>

        </x-forms.vertical>

    </x-auth-box>

</x-layouts.guest>
