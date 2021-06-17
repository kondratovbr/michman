<x-action-section>
    <x-slot name="content">
        <h3 class="text-lg font-medium">
            <x-icon><i class="{{ $icon }}"></i></x-icon>
            <span class="ml-1">
                {{ $title }}
            </span>
        </h3>
        @if(! $connected)
            <x-buttons.primary class="mt-3">
                {{ __('account.vcs.connect-to-button', ['provider' => $title]) }}
            </x-buttons.primary>
        @else
            <p class="mt-2 text-green-400">
                <x-icon><i class="fas fa-check"></i></x-icon>
                <span class="ml-1">
                    {{ __('account.vcs.connected') }}
                </span>
            </p>
            <x-buttons class="mt-3">
                <x-buttons.primary>
                    {{ __('account.vcs.refresh-button') }}
                </x-buttons.primary>
                <x-buttons.secondary>
                    {{ __('account.vcs.unlink-button') }}
                </x-buttons.secondary>
            </x-buttons>
        @endisset
    </x-slot>
</x-action-section>
