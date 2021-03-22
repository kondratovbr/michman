<x-action-section>

    <x-slot name="title">
        {{ __('account.profile.sessions.title') }}
    </x-slot>

    <x-slot name="description">
        {{ __('account.profile.sessions.description') }}
    </x-slot>

    <x-slot name="content">
        <div class="max-w-xl text-sm">
            {{ __('account.profile.sessions.explanation') }}
        </div>

        {{-- Browser Sessions List - works only when sessions are stored in DB. --}}
        @if (count($this->sessions) > 0)
            <div class="mt-5">
                @include('profile._other-sessions-list')
            </div>
        @endif

        <div class="flex items-center mt-5">
            <x-button wire:click="confirmLogout" wire:loading.attr="disabled">
                {{ __('account.profile.sessions.logout') }}
            </x-button>

{{--            TODO: Change this to some animated icon. In every single other place as well.--}}
            <x-jet-action-message class="ml-3" on="loggedOut">
                {{ __('misc.done') }}
            </x-jet-action-message>
        </div>

        {{-- Log Out Other Devices Confirmation Modal --}}
        @include('profile._logout-sessions-modal')

    </x-slot>

</x-action-section>
