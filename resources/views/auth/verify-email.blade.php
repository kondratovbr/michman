{{--TODO: IMPORTANT! Unfinished!--}}

<x-layouts.guest>
    <x-auth-box>

        <h3 class="text-lg font-medium">
            {{ __('auth.thanks-for-registration') }}
        </h3>

        <p class="mt-4">
            {{ __('auth.verify-email') }}
        </p>

        @if (session('status') == 'verification-link-sent')
            <div class="mt-4 text-green-500">
                {{ __('auth.verification-link-sent') }}
            </div>
        @endif

        <div class="mt-4 flex items-center justify-between">

            <x-form method="POST" action="{{ route('verification.send') }}">
                <x-buttons.secondary type="submit">
                    {{ __('auth.resend-verification-link-button') }}
                </x-buttons.secondary>
            </x-form>

            <x-form method="POST" action="{{ route('logout') }}">
                <x-buttons.text type="submit">
                    {{ __('auth.logout') }}
                </x-buttons.text>
            </x-form>

        </div>
    </x-auth-box>
</x-layouts.guest>
