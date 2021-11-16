<x-layouts.guest>
    <x-auth-box>

        <x-slot name="title">
            {{ __('auth.login-to', ['app' => siteName()]) }}
        </x-slot>

        <x-validation-errors class="mb-4" />

{{--        TODO: CRITICAL! What is this here for? Is it even used? Does it look OK? Check other auth pages as well.--}}
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
                    which we don't want to show here - it will be shown on the top block instead. --}}
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
                    <x-link>{{ __('auth.forgot-your-password') }}</x-link>
                @endif

                <x-buttons.primary class="ml-4">
                    {{ __('buttons.login') }}
                </x-buttons.primary>
            </div>

        </x-forms.vertical>

        <x-hr>{{ __('misc.or') }}</x-hr>

        <div class="flex flex-col items-center">
            <h3>{{ __('auth.login-via') }}</h3>
            <x-oauth-buttons class="mt-2"/>
        </div>

        <x-slot name="bottomMessage">
            <div class="flex justify-center space-x-2">
                <p>{{ __('auth.new-to', ['app' => siteName()]) }}</p>
                <x-link href="{{ route('register') }}">{{ __('auth.register') }}</x-link>
            </div>
        </x-slot>

    </x-auth-box>
</x-layouts.guest>
