{{--TODO: IMPORTANT! The notifications should show up somewhere on the screen regardless of the scroll position. I.e. not like now, not on top of the page. Check out how other apps do this. Forge's way is lazy.--}}

{{--TODO: VERY IMPORTANT! Check out how this works on mobile.--}}

<div class="{{ $notifications->isEmpty() ? '' : 'mb-8' }}">
    @unless($notifications->isEmpty())
        <x-table-section>

            <x-slot name="title">{{ __('notifications.title') }}</x-slot>

            <x-slot name="header">
                <x-tr-header>
                    <x-th>{{ __('notifications.time') }}</x-th>
                    <x-th>{{ __('notifications.message') }}</x-th>
                    <x-th></x-th>
                </x-tr-header>
            </x-slot>

            <x-slot name="body">
{{--                TODO: Add "levels" to these notifications and change highlight colors accordingly.--}}
                @foreach($notifications as $notification)
                    <x-tr>
{{--                        TODO: Should make this to show diff only when the notification is recent. For older ones it would be more convenient to see the exact date and time.--}}
                        <x-td>{{ $notification->createdAt->diffForHumans() }}</x-td>
                        <x-td>{!! $notification->message !!}</x-td>
                        <x-td>
                            <div class="flex justify-end items-center space-x-2">
                                @if($notification->viewable())
                                    <x-buttons.see
                                        wire:click.prevent="details('{{ $notification->id }}')"
                                        wire:key="details-button-{{ $notification->id }}"
                                    />
                                @endif
                                <x-buttons.trash
                                    wire:click.prevent="trash('{{ $notification->id }}')"
                                    wire:key="trash-button-{{ $notification->id }}"
                                />
                            </div>
                        </x-td>
                    </x-tr>
                @endforeach
            </x-slot>

            @if($modalOpen)
                <x-slot name="modal">
                    <x-modals.dialog wire:model="modalOpen" modalId="notificationDetailsModal">
                        <x-slot name="header">
                            <h3>{{ __('notifications.details-title') }}</h3>
                        </x-slot>

                        <x-slot name="content">
                            {!! $this->detailsView !!}
                        </x-slot>

                        <x-slot name="actions">
                            <x-buttons.secondary
                                x-on:click.stop="$dispatch('close-modal')"
                                wire:loading.attr="disabled"
                            >
                                {{ __('buttons.close') }}
                            </x-buttons.secondary>
                        </x-slot>
                    </x-modals.dialog>
                </x-slot>
            @endif

        </x-table-section>
    @endunless
</div>
