<x-action-section>
    <x-slot name="content">

        <h3 class="text-lg font-medium">
            <x-icon><i class="{{ config("vcs.list.{$provider}.icon") }}"></i></x-icon>
            <span class="ml-1">
                {{ __("auth.oauth.providers.{$provider}.label") }}
            </span>
        </h3>

        @if(is_null(user()->vcs($provider)))

            <x-buttons.primary
                class="mt-3"
                :link="true"
                href="{{ route('vcs.redirect', $provider) }}"
            >
                {{ __('account.vcs.connect-to-button', ['provider' => __("auth.oauth.providers.{$provider}.label")]) }}
            </x-buttons.primary>

        @else

            <p class="mt-2 text-green-400">
                <x-icon><i class="fas fa-check"></i></x-icon>
                <span class="ml-1">
                    {{ __('account.vcs.connected', ['username' => user()->vcs($provider)->nickname]) }}
                </span>
            </p>
            <x-buttons class="mt-3">
                <x-buttons.primary
                    :link="true"
                    href="{{ route('vcs.redirect', $provider) }}"
                >
                    {{ __('account.vcs.refresh-button') }}
                </x-buttons.primary>
                <x-buttons.secondary
                    :link="true"
                    href="{{ route('vcs.unlink', $provider) }}"
                >
                    {{ __('account.vcs.unlink-button') }}
                </x-buttons.secondary>
            </x-buttons>

        @endisset
    </x-slot>
</x-action-section>
