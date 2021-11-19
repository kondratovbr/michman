{{--TODO: CRITICAL! Adjust size for different screens.--}}
<x-smallbox class="w-40">
    <x-slot name="content">
        <div class="h-full w-full flex flex-col items-center">

            <div class="flex flex-col items-center">
                <span>{{ __("auth.oauth.providers.{$provider}.label") }}</span>
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
                <x-buttons.secondary>
                    {{ __('auth.unlink-button') }}
                </x-buttons.secondary>
            @else
                <x-buttons.primary class="mt-auto">
                    {{ __('auth.link-button') }}
                </x-buttons.primary>
            @endif

        </div>
    </x-slot>
</x-smallbox>
