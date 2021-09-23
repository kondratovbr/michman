<x-action-section>
    <x-slot name="title">{{ __('projects.quick-deploy.title') }}</x-slot>

    <x-slot name="content">

        <div class="space-y-6">
            <x-message colors="info">{{ __('projects.quick-deploy.info') }}</x-message>

            @if(is_null($this->hook) || $this->hook->isEnabling())
                <x-buttons.primary
                    wire:click.prevent="enable"
                    wire:loading.attr="disabled"
                    :loading="$this->hook?->isEnabling()"
                >{{ __('projects.quick-deploy.enable') }}</x-buttons.primary>
            @else
                <x-buttons.danger
                    wire:click.prevent="disable"
                    wire:loading.attr="disabled"
                    :loading="$this->hook->isDeleting()"
                >{{ __('projects.quick-deploy.disable') }}</x-buttons.danger>
            @endif

        </div>

    </x-slot>
</x-action-section>
