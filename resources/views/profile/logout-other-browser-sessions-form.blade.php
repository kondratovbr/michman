{{--TODO: IMPORTANT! Unfinished!--}}

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

        {{-- Browser Sessions List - shown if there are others besides the current one. --}}
        {{-- Works only when sessions are stored in DB. --}}
        @if (count($this->sessions) > 0)
            <div class="mt-5 space-y-6">
                @foreach ($this->sessions as $session)
                    <div class="flex items-center">
                        <div>
                            @if ($session->agent->isDesktop())
                                <x-icon size="10">
                                    <i class="fa fa-2x fa-desktop"></i>
                                </x-icon>
                            @else
                                <x-icon size="10">
                                    <i class="fa fa-2x fa-mobile-alt"></i>
                                </x-icon>
                            @endif
                        </div>

                        <div class="ml-3">
                            <div>
                                {{ $session->agent->platform() }} - {{ $session->agent->browser() }}
                            </div>
                            <div>
                                {{ $session->ip_address }},

                                @if ($session->is_current_device)
                                    <span class="text-green-400 font-semibold">
                                        {{ __('account.profile.sessions.this_device') }}
                                    </span>
                                @else
                                    {{ __('account.profile.sessions.last_active') }}
                                    {{ $session->last_active }}
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="flex items-center mt-5">
            <x-button wire:click="confirmLogout" wire:loading.attr="disabled">
                {{ __('account.profile.sessions.logout') }}
            </x-button>

{{--            TODO: Change this to some animated icon. In every single other place as well.--}}
            <x-jet-action-message class="ml-3" on="loggedOut">
                {{ __('Done.') }}
            </x-jet-action-message>
        </div>

        {{-- Log Out Other Devices Confirmation Modal --}}
        @include('profile._logout-sessions-modal')

    </x-slot>

</x-action-section>
