<x-action-section>
    <x-slot name="title">{{ __('projects.quick-deploy.title') }}</x-slot>

    <x-slot name="content">

        <div class="space-y-6">
            <x-message colors="info">{{ __('projects.quick-deploy.info') }}</x-message>

            @cannot('create', [App\Models\Webhook::class, $project])
                <p class="text-sm"><x-lang key="deployments.no-subscription" /></p>
            @elseif(is_null($this->hook) || $this->hook->isEnabling())
                <x-buttons.primary
                    wire:click.prevent="enable"
                    wire:loading.attr="disabled"
                    :loading="$this->hook?->isEnabling()"
                    :disabled="! user()->appEnabled()"
                >{{ __('projects.quick-deploy.enable') }}</x-buttons.primary>
            @else
                <x-buttons.danger
                    wire:click.prevent="disable"
                    wire:loading.attr="disabled"
                    :loading="$this->hook->isDeleting()"
                    :disabled="! user()->appEnabled()"
                >{{ __('projects.quick-deploy.disable') }}</x-buttons.danger>
            @endif

        </div>

    </x-slot>
</x-action-section>
