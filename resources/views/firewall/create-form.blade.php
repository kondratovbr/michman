<x-form-section submit="store">

    <x-slot name="title">{{ __('servers.firewall.form.title') }}</x-slot>

    <x-slot name="form">
        <div class="space-y-6">

            <x-message class="max-w-prose" colors="info">
                <x-lang key="firewall.explanation"/>
            </x-message>

            <x-field class="max-w-sm">
                <x-label>{{ __('servers.firewall.form.name.title') }}</x-label>
                <x-inputs.text
                    name="name"
                    wire:model.defer="name"
                    placeholder="WHOIS"
                />
                <x-input-error for="name" />
            </x-field>

            <x-field class="max-w-sm">
                <x-label>{{ __('servers.firewall.form.port.title') }}</x-label>
                <x-inputs.text
                    name="port"
                    wire:model.defer="port"
                    placeholder="43"
                />
                <x-input-error for="port" />
                <x-help>{{ __('servers.firewall.form.port.help') }}</x-help>
            </x-field>

            <x-field class="max-w-sm">
                <x-label>{{ __('servers.firewall.form.from-ip.title') }}</x-label>
                <x-inputs.text
                    name="from_ip"
                    wire:model.defer="from_ip"
                />
                <x-input-error for="from_ip" />
                <x-help>{{ __('servers.firewall.form.from-ip.help') }}</x-help>
            </x-field>

            <div class="flex">
                <span>{{ __('servers.firewall.table.type') }}:</span>
                <x-badge colors="success" class="ml-2">{{ __('servers.firewall.table.allow') }}</x-badge>
            </div>

        </div>
    </x-slot>

    <x-slot name="actions">
        <x-buttons.primary
            wire:click.prevent="store"
            wire:loading.attr="disabled"
        >
            {{ __('servers.firewall.form.button') }}
        </x-buttons.primary>
    </x-slot>

</x-form-section>
