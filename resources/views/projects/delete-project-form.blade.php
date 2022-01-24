<x-action-section>
    <x-slot name="title">
        {{ __('projects.manage.delete.title') }}
    </x-slot>

    <x-slot name="content">
        <x-message colors="warning">
            {{ __('projects.manage.delete.info') }}
        </x-message>

        <div class="mt-6 flex justify-end">
            <x-buttons.danger
                wire:click="openConfirmationModal"
                wire:loading.attr="disabled"
            >
                {{ __('projects.manage.delete.button') }}
            </x-buttons.danger>
        </div>

        <x-modals.dialog wire:model="confirmationModalOpen">
            <x-slot name="header">
                {{ __('projects.manage.delete.modal.title', ['project' => $project->domain]) }}
            </x-slot>

            <x-slot name="content">
                <div class="space-y-6">
                    <x-message colors="warning">
                        {{ __('projects.manage.delete.info') }}
                    </x-message>

                    <x-field>
                        <x-label for="projectName">
                            {{ __('projects.manage.delete.modal.field-label', ['project' => $project->domain]) }}
                        </x-label>
                        <x-inputs.text
                            class="max-w-md"
                            name="projectName"
                            wire:model="projectName"
                        />
                        <x-input-error for="projectName"/>
                    </x-field>
                </div>
            </x-slot>

            <x-slot name="actions">
                <div class="flex justify-between items-center space-x-3">
                    <x-buttons.danger
                        wire:click="delete"
                        wire:loading.attr="disabled"
                    >
                        <div>
                            <span>{{ __('projects.manage.delete.button') }}</span>
                            <span class="normal-case">{{ $project->domain }}</span>
                        </div>
                    </x-buttons.danger>
                    <x-buttons.secondary
                        wire:click="$toggle('confirmationModalOpen')"
                        wire:loading.attr="disabled"
                    >
                        {{ __('buttons.cancel') }}
                    </x-buttons.secondary>
                </div>
            </x-slot>
        </x-modals.dialog>

    </x-slot>
</x-action-section>
