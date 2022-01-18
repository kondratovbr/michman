<x-action-section>
    <x-slot name="title">{{ __('projects.uninstall.title') }}</x-slot>

    <x-slot name="content">

        <x-message colors="warning">{{ __('projects.uninstall.info') }}</x-message>

        <div class="mt-6 flex justify-end">
            <x-buttons.danger
                wire:click.prevent="uninstall"
                wire:loading.attr="disabled"
                :disabled="Gate::denies('update', $project)"
            >{{ __('projects.uninstall.button') }}</x-buttons.danger>
        </div>

    </x-slot>
</x-action-section>
