{{--TODO: IMPORTANT! Unfinished!--}}
{{--TODO: Add links to TOTP apps for iOS/Android. And maybe for a CLI app as well.--}}
{{--TODO: Definitely needs some loading animation and some transitions. Maybe just move most of it into a modal.--}}

<x-action-section>

    <x-slot name="title">
        {{ __('account.profile.tfa.title') }}
    </x-slot>

    <x-slot name="description">
        {{ __('account.profile.tfa.description') }}
    </x-slot>

    <x-slot name="content">
        <h3 class="text-lg font-medium">
            {{ $this->enabled ? __('account.profile.tfa.enabled') : __('account.profile.tfa.disabled') }}
        </h3>

        <div class="mt-3 max-w-xl text-sm">
            <p>
                {{ __('account.profile.tfa.explanation') }}
            </p>
        </div>

        @if ($this->enabled)
            @if ($showingQrCode)
                <div class="mt-4 max-w-xl text-sm">
                    <p class="font-semibold">
                        {{ __('account.profile.tfa.scan-this') }}
                    </p>
                </div>

                <div class="my-4 flex justify-center">
{{--                    TODO: Don't forget to disable bg and p in light mode, as it was in Jetstream.--}}
{{--                <div class="mt-4 dark:p-4 dark:w-56 dark:bg-white">--}}
                    <div class="w-full max-w-md p-4 bg-white">
                        <figure class="block relative w-full aspect-h-1 aspect-w-1">
{{--                        TODO: Make the thing a bit more colorful. Maybe more interesting design-wise. To fit the branding.
                                See docs: https://www.simplesoftware.io/#/docs/simple-qrcode--}}
                            {!! $this->user->twoFactorQrCodeSvg(size: 192, margin: 0) !!}
                        </figure>
                    </div>
                </div>
            @endif

            @if ($showingRecoveryCodes)
                <div class="mt-4 max-w-xl text-sm">
                    <p class="font-semibold">
                        {{ __('account.profile.tfa.recovery-explanation') }}
                    </p>
                </div>

{{--                TODO: Add a small "Copy to clipboard" button.--}}
                <div class="grid gap-1 max-w-xl mt-4 px-4 py-4 font-mono text-sm bg-gray-900 rounded-lg">
                    @foreach (json_decode(decrypt($this->user->two_factor_recovery_codes), true) as $code)
                        <div>{{ $code }}</div>
                    @endforeach
                </div>
            @endif
        @endif

        <div class="mt-5">
            @if (! $this->enabled)
                <x-password-confirmation wire:then="enableTwoFactorAuthentication">
                    <x-buttons.primary type="button" wire:loading.attr="disabled">
                        {{ __('account.profile.tfa.enable') }}
                    </x-buttons.primary>
                </x-password-confirmation>
            @else
                @if ($showingRecoveryCodes)
                    <x-password-confirmation wire:then="regenerateRecoveryCodes">
                        <x-buttons.secondary class="mr-3">
                            {{ __('account.profile.tfa.regenerate-recovery') }}
                        </x-buttons.secondary>
                    </x-password-confirmation>
                @else
                    <x-password-confirmation wire:then="showRecoveryCodes">
                        <x-buttons.secondary class="mr-3">
                            {{ __('account.profile.tfa.show-recovery') }}
                        </x-buttons.secondary>
                    </x-password-confirmation>
                @endif

                <x-password-confirmation wire:then="disableTwoFactorAuthentication">
                    <x-buttons.danger wire:loading.attr="disabled">
                        {{ __('account.profile.tfa.disable') }}
                    </x-buttons.danger>
                </x-password-confirmation>
            @endif
        </div>
    </x-slot>

</x-action-section>
