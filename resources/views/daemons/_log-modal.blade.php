{{--TODO: This modal needs an "X" button in the corner, like Forge does. Just in case.--}}

<x-modals.dialog wireModel="modalOpen" modalId="viewDaemonLogModal">

    <x-slot name="header">
        <h3>{{ __('servers.daemons.log-modal-title') }}</h3>
    </x-slot>

    <x-slot name="content">
        @if($error)
            <span>{{ __('servers.daemons.failed-to-retrieve-logs') }}</span>
        @else
            @if(empty($log ?? null))
                <x-spinner/>
            @else
                <x-code-block :wrap="true">{!! $log !!}</x-code-block>
            @endif
        @endif
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
