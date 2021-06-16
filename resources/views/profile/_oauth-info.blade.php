<x-action-section>
    <x-slot name="title">
        {{ __('account.profile.oauth.title', [
            'provider' => __("auth.oauth.providers.{$oauthProvider}.label"),
        ]) }}
    </x-slot>

    <x-slot name="content">
        <h3 class="text-lg font-medium">
            {{ __('account.profile.oauth.authenticated', [
                'provider' => __("auth.oauth.providers.{$oauthProvider}.label"),
            ]) }}
        </h3>

        <p class="mt-3 max-w-prose text-sm">
            {{ __('account.profile.oauth.explanation-start', [
                'provider' => __("auth.oauth.providers.{$oauthProvider}.label"),
            ]) }}
            <x-link href="{{ route('password.request') }}">
                {{ __('auth.restore-password') }}
            </x-link>
            {{ __('account.profile.oauth.explanation-end') }}
        </p>
    </x-slot>

</x-action-section>
