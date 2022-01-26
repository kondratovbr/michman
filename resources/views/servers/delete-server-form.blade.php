<x-action-section>
    <x-slot name="title">
        {{ __('servers.manage.delete.title') }}
    </x-slot>

    <x-slot name="content">
        <x-message colors="warning">
            {{ __('servers.manage.delete.info', ['provider' => $server->provider->localName]) }}
        </x-message>

        <div class="mt-6 flex justify-end">
            <x-buttons.danger
                wire:click="openConfirmationModal"
                wire:loading.attr="disabled"
            >
                {{ __('servers.manage.delete.button') }}
            </x-buttons.danger>
        </div>

        <x-modals.dialog wire:model="confirmationModalOpen">
            <x-slot name="header">
                {{ __('servers.manage.delete.modal.title', ['server' => $server->name]) }}
            </x-slot>

            <x-slot name="content">
                <div class="space-y-6">
                    <x-message colors="warning">
                        {{ __('servers.manage.delete.info', ['provider' => $server->provider->localName]) }}
                    </x-message>

                    <x-field>
                        <x-label for="serverName">
                            {{ __('servers.manage.delete.modal.field-label', ['server' => $server->name]) }}
                        </x-label>
                        <x-inputs.text
                            class="max-w-md"
                            name="serverName"
                            wire:model="serverName"
                        />
                        <x-input-error for="serverName" />
                    </x-field>
                </div>
            </x-slot>

            <x-slot name="actions">
                <div class="flex justify-between items-center space-x-3">
{{--                    TODO: Pressing "Enter" doesn't work here.--}}
                    <x-buttons.danger
                        wire:click="delete"
                        wire:loading.attr="disabled"
                    >
                        <div>
                            <span>{{ __('servers.manage.delete.button') }}</span>
                            <span class="normal-case">{{ $server->name }}</span>
                        </div>
                    </x-buttons.danger>
                    <x-buttons.secondary wire:click="$toggle('confirmationModalOpen')">
                        {{ __('buttons.cancel') }}
                    </x-buttons.secondary>
            </x-slot>
        </x-modals.dialog>
    </x-slot>
</x-action-section>
