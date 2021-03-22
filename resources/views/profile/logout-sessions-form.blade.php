{{--TODO: IMPORTANT! Unfinished!--}}

<x-action-section>
    <x-slot name="title">
        {{ __('account.profile.sessions.title') }}
    </x-slot>
    <x-slot name="description">
        {{ __('account.profile.sessions.description') }}
    </x-slot>

    <x-slot name="content">
        <div class="max-w-prose text-sm">
            {{ __('account.profile.sessions.explanation') }}
        </div>

        {{-- Browser Sessions List - works only when sessions are stored in DB. --}}
        @if (count($this->sessions) > 0)
            <div class="mt-5">
                @include('profile._other-sessions-list')
            </div>
        @endif

        <div class="flex items-center mt-5 space-x-3">
            <x-buttons.primary
                wire:click="openModal"
                wire:loading.attr="disabled"
            >
                {{ __('account.profile.sessions.logout') }}
            </x-buttons.primary>
            <x-action-message on="loggedOut">{{ __('misc.done') }}</x-action-message>
        </div>

        @include('profile._logout-sessions-modal')

    </x-slot>

</x-action-section>
