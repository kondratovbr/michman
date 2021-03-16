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

        <x-button
            class="mt-5"
            wire:click="openModal"
            wire:loading.attr="disabled"
        >
            Test new modal
        </x-button>
        @include('profile._logout-sessions-modal-new')

    </x-slot>

</x-action-section>
