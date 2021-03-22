{{--TODO: IMPORTANT! Unfinished!--}}

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

                <div class="mt-4 dark:p-4 dark:w-56 dark:bg-white">
                    {!! $this->user->twoFactorQrCodeSvg() !!}
                </div>
            @endif

            @if ($showingRecoveryCodes)
                <div class="mt-4 max-w-xl text-sm">
                    <p class="font-semibold">
                        {{ __('account.profile.tfa.recovery-explanation') }}
                    </p>
                </div>

                <div class="grid gap-1 max-w-xl mt-4 px-4 py-4 font-mono text-sm bg-gray-100 rounded-lg">
                    @foreach (json_decode(decrypt($this->user->two_factor_recovery_codes), true) as $code)
                        <div>{{ $code }}</div>
                    @endforeach
                </div>
            @endif
        @endif

        <div class="mt-5">
            @if (! $this->enabled)
                <x-jet-confirms-password wire:then="enableTwoFactorAuthentication">
                    <x-buttons.primary type="button" wire:loading.attr="disabled">
                        {{ __('account.profile.tfa.enable') }}
                    </x-buttons.primary>
                </x-jet-confirms-password>
            @else
                @if ($showingRecoveryCodes)
                    <x-jet-confirms-password wire:then="regenerateRecoveryCodes">
                        <x-buttons.secondary class="mr-3">
                            {{ __('account.profile.tfa.regenerate-recovery') }}
                        </x-buttons.secondary>
                    </x-jet-confirms-password>
                @else
                    <x-jet-confirms-password wire:then="showRecoveryCodes">
                        <x-buttons.secondary class="mr-3">
                            {{ __('account.profile.tfa.show-recovery') }}
                        </x-buttons.secondary>
                    </x-jet-confirms-password>
                @endif

                <x-jet-confirms-password wire:then="disableTwoFactorAuthentication">
                    <x-buttons.danger wire:loading.attr="disabled">
                        {{ __('account.profile.tfa.disable') }}
                    </x-buttons.danger>
                </x-jet-confirms-password>
            @endif
        </div>
    </x-slot>

</x-action-section>
