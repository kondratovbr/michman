@props(['wireModel'])

<x-modal-new wire:model="{{ $wireModel }}">

{{--    TODO: IMPORTANT! Unfinished! Figure out responsiveness and mobile/touch!--}}
    <div class="grid grid-cols-12">
        <x-box class="col-start-4 col-end-10">
            <x-box.form {{ $attributes }}>

                @isset($header)
                    <x-slot name="header">{{ $header }}</x-slot>
                @endisset

                <x-slot name="content">
                    {{ $content ?? $slot }}
                </x-slot>

                @isset($actions)
                    <x-slot name="actions">{{ $actions }}</x-slot>
                @endisset

            </x-box.form>
        </x-box>
    </div>

</x-modal-new>
