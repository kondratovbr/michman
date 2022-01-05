<x-layouts.guest>
    <x-auth-box>

        <x-slot name="title">
            {{ __('auth.forgot-your-password') }}
        </x-slot>

        <div class="mb-4 text-sm">
            {{ __('auth.forgot-password-info') }}
        </div>

        @if (session('status'))
            <x-message class="mb-4" colors="info">
                {{ session('status') }}
            </x-message>
        @endif

        <x-validation-errors class="mb-4" />

        <x-forms.vertical method="POST" action="{{ route('password.email') }}">

            <x-field>
                <x-label for="email">Email</x-label>
                <x-inputs.email
                    name="email"
                    required
                    autofocus
                />
            </x-field>

            <div class="flex items-center justify-end mt-4">
{{--                TODO: VERY IMPORTANT! Make sure this cannot be abused. I.e. use some harsh rate-limiting.--}}
                <x-buttons.primary>
                    {{ __('auth.email-password-reset') }}
                </x-buttons.primary>
            </div>

        </x-forms.vertical>

    </x-auth-box>
</x-layouts.guest>
