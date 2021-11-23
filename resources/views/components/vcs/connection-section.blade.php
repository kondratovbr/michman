<x-action-section {{ $attributes->class(['w-full lg:w-4/5 2xl:w-2/3']) }}>
    <x-slot name="content">

        <h3 class="text-lg font-medium">
            <x-icon><i class="{{ config("auth.oauth_providers.{$oauthProvider}.icon") }}"></i></x-icon>
            <span class="ml-1">
                {{ __("auth.oauth.providers.{$oauthProvider}.label") }}
            </span>
        </h3>

        @if(is_null($vcsProvider))

            <x-buttons.primary
                class="mt-3"
                :link="true"
                href="{{ route('vcs.link', $oauthProvider) }}"
            >
                {{ __('account.vcs.connect-to-button', ['provider' => __("auth.oauth.providers.{$oauthProvider}.label")]) }}
            </x-buttons.primary>

        @else

            <p class="mt-2 text-green-400">
                <x-icon><i class="fas fa-check"></i></x-icon>
                <span class="ml-1">
                    {{ __('account.vcs.connected', ['username' => user()->vcs($vcsProviderName)->nickname]) }}
                </span>
            </p>
            <div class="mt-3 flex items-center space-x-3">
                <x-buttons.primary
                    :link="true"
                    href="{{ route('vcs.link', $oauthProvider) }}"
                >
                    {{ __('account.vcs.refresh-button') }}
                </x-buttons.primary>
                <x-buttons.secondary
                    :link="$canUnlink()"
                    :disabled="! $canUnlink()"
                    href="{{ route('vcs.unlink', $oauthProvider) }}"
                >
                    {{ __('account.vcs.unlink-button') }}
                </x-buttons.secondary>
                @if($inUse())
                    <span class="text-sm text-gray-300">
                        @if($vcsProvider->projects->count() == 1)
                            {{ __('account.vcs.used-by-project', ['project' => $vcsProvider->projects->first()->domain]) }}
                        @else
                            {{ trans_choice('account.vcs.used-by-n-projects', $vcsProvider->projects) }}
                        @endif
                    </span>
                @endif
            </div>

        @endisset
    </x-slot>
</x-action-section>
