{{--TODO: This block looks meh in general and especially on smaller screens and with long nicknames shown. Should redesign the thing to fit longer nicknames and look nicer on smaller screens. --}}
<x-smallbox class="w-40">
    <x-slot name="content">
        <div class="h-full w-full flex flex-col items-center">

            <div class="flex flex-col items-center">
                <span>{{ $this->providerLabel }}</span>
                <x-icon class="mt-1 mb-3" size="16">
                    <i class="{{ config("auth.oauth_providers.{$provider}.icon") }} fa-4x"></i>
                </x-icon>
            </div>

            @if(Auth::user()->oauth($provider))
                <div class="mb-3 flex flex items-center text-green-400">
                    <x-icon><i class="fas fa-check"></i></x-icon>
                    <span class="ml-2">
                        {{ __('auth.oauth-linked-to') }}<br>
                        {{ Auth::user()->oauth($provider)->nickname }}
                    </span>
                </div>
                <x-buttons.secondary wire:click="$toggle('confirmationModalOpen')" :disabled="! $this->canUnlink">
                    {{ __('auth.unlink-button') }}
                </x-buttons.secondary>
            @else
                <x-buttons.primary
                    class="mt-auto"
                    :link="true"
                    href="{{ route('oauth.link', $provider) }}"
                >
                    {{ __('auth.link-button') }}
                </x-buttons.primary>
            @endif

        </div>

        <x-modals.small wire:model="confirmationModalOpen">
            <x-slot name="header">
                {{ __('auth.oauth.unlink-provider-oauth', ['provider' => $this->providerLabel]) }}
            </x-slot>

            <x-slot name="content">
                {{ __('auth.oauth.are-you-sure', ['provider' => $this->providerLabel]) }}
            </x-slot>

            <x-slot name="actions">
                <div class="flex-grow flex justify-between">
                    <x-buttons.secondary
                        wire:click="$toggle('confirmationModalOpen')"
                        wire:loading.attr="disabled"
                    >
                        {{ __('buttons.cancel') }}
                    </x-buttons.secondary>

                    <x-buttons.danger :link="true" href="{{ route('oauth.unlink', $provider) }}">
                        {{ __('auth.oauth.unlink-provider-button', ['provider' => $this->providerLabel]) }}
                    </x-buttons.danger>
                </div>
            </x-slot>
        </x-modals.small>

    </x-slot>
</x-smallbox>
