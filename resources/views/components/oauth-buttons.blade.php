{{--TODO: CRITICAL! Make sure to either implement all listed options or remove/disable the temporary unsupported ones.--}}

<div {{ $attributes->class([
    'flex space-x-4',
]) }}>
    @foreach(config('auth.oauth_providers') as $oauthProviderName => $oauthProviderConfig)
        <x-buttons.secondary :link="true" href="{{ route('oauth.login', $oauthProviderName) }}">
            <div class="flex flex-col items-center justify-center">
                <x-icon class="text-5xl" size="16">
                    <i class="{{ $oauthProviderConfig['icon'] }}"></i>
                </x-icon>
                <p>{{ __("auth.oauth.providers.{$oauthProviderName}.label") }}</p>
            </div>
        </x-buttons.secondary>
    @endforeach
</div>
