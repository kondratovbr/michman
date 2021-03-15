<x-modal-new>

    <x-box>
        <x-box.form>

            @isset($header)
                <x-slot name="header">{{ $header }}</x-slot>
            @endisset

            {{ $content ?? $slot }}

            @isset($actions)
                <x-slot name="actions">{{ $actions }}</x-slot>
            @endisset

        </x-box.form>
    </x-box>

</x-modal-new>
