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

        <div class="mt-3 max-w-prose text-sm space-y-3">
            <p>
                {{ __('account.profile.oauth.review-and-revoke') }}
                <x-link
                    href="{{ config('services.' . $oauthProvider . '.review_access_url') }}"
                    :external="true"
                >{{ __("auth.oauth.providers.{$oauthProvider}.review-page-link") }}</x-link>
            </p>

{{--            TODO: IMPORTANT! Implement and uncomment this paragraph. Right now "Restore Password" function doesn't work for authenticated users.--}}
{{--            <p>--}}
{{--                {{ __('account.profile.oauth.explanation-start', [--}}
{{--                    'provider' => __("auth.oauth.providers.{$oauthProvider}.label"),--}}
{{--                ]) }}--}}
{{--                <x-link href="{{ route('password.request') }}">--}}
{{--                    {{ __('auth.restore-password') }}--}}
{{--                </x-link>--}}
{{--                {{ __('account.profile.oauth.explanation-end') }}--}}
{{--            </p>--}}

        </div>
    </x-slot>

</x-action-section>
