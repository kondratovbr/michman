<x-form-section submit="store">

    <x-slot name="title">{{ __('account.ssh.create.title') }}</x-slot>

    <x-slot name="description">{{ __('account.ssh.create.description') }}</x-slot>

    <x-slot name="form">
        <div class="space-y-6">

            <x-field class="max-w-sm">
                <x-label>{{ __('account.ssh.name.title') }}</x-label>
                <x-inputs.text
                    name="state.name"
                    wire:model.defer="state.name"
                    placeholder="{{ ucfirst(user()->name) }}'s Laptop"
                />
                <x-input-error for="state.name" />
            </x-field>

            <x-field>
                <x-label>Public Key</x-label>
                <x-inputs.textarea
                    name="state.publicKey"
                    wire:model.defer="state.publicKey"
                    placeholder="ssh-rsa ... {{ strtolower(user()->name) }}@laptop.local"
                />
                <x-input-error for="state.publicKey" />
            </x-field>

        </div>
    </x-slot>

    <x-slot name="actions">
        <x-buttons.primary
            wire:click.prevent="store"
            wire:loading.attr="disabled"
        >
            {{ __('projects.create.form.button') }}
        </x-buttons.primary>
    </x-slot>

</x-form-section>
