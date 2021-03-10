{{--TODO: IMPORTANT! Unfinished!--}}

<x-layouts.guest>
    <x-auth-box>

{{--        TODO: This text looks like a horrible mess and hard to read. Fix. Also, the box probably needs some header. --}}

        <div class="mb-4 text-sm text-gray-600">
            {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
        </div>

        @if (session('status'))
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ session('status') }}
            </div>
        @endif

        <x-jet-validation-errors class="mb-4" />

        <x-form method="POST" action="{{ route('password.email') }}">

            <x-field>
                <x-label for="email" value="{{ __('Email') }}" />
                <x-inputs.email
                    name="email"
                    required
                    autofocus
                />
            </x-field>

            <div class="flex items-center justify-end mt-4">
                <x-button>
                    {{ __('Email Password Reset Link') }}
                </x-button>
            </div>

        </x-form>

    </x-auth-box>
</x-layouts.guest>
