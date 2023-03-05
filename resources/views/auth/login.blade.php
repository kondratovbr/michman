<x-layouts.guest>
    <x-auth-box>

        <x-slot name="title">
            {{ __('auth.login-to', ['app' => siteName()]) }}
        </x-slot>

        <x-validation-errors class="mb-4" />

        @if (session('status'))
            <x-message class="mb-4" colors="info">
                {{ session('status') }}
            </x-message>
        @endif

        <x-forms.vertical method="POST" action="{{ route('login') }}">

            <x-honeypot/>

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
                <x-link href="{{ route('password.request') }}">{{ __('auth.forgot-your-password') }}</x-link>

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
