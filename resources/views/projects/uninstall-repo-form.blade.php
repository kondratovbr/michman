<x-action-section>
    <x-slot name="title">Uninstall Repository</x-slot>

    <x-slot name="content">

        <x-message colors="danger">Foobar</x-message>

        <div class="mt-6 flex justify-end">
            <x-buttons.danger
                wire:click.prevent="uninstall"
                wire:loading.attr="disabled"
                disabled
            >
                Uninstall Repository
            </x-buttons.danger>
        </div>

    </x-slot>
</x-action-section>
