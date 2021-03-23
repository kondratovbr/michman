@props(['wireModel', 'modalId'])

<x-modal-new
    wire:model="{{ $wireModel }}"
    id="{{ $modalId ?? md5($attributes->wire('model')) }}"
>

{{--    TODO: IMPORTANT! Unfinished! Figure out responsiveness and mobile/touch!--}}
    <div class="grid grid-cols-12">

        <x-box
            class="col-start-4 col-end-10"
            {{-- To prevent the modal closing on clicks inside the modal box itself --}}
            x-on:click.stop=""
        >

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
