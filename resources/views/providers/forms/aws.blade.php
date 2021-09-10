<div class="space-y-4">

    <x-field class="max-w-xl">
        <x-label>{{ __('account.providers.key.label') }}</x-label>
        <x-inputs.text
            name="key"
            wire:model.defer="key"
            x-bind:disabled="provider !== 'aws'"
        />
        <x-input-error for="key" />
    </x-field>

    <x-field class="max-w-xl">
        <x-label>{{ __('account.providers.secret.label') }}</x-label>
        <x-inputs.text
            name="secret"
            wire:model.defer="secret"
            x-bind:disabled="provider !== 'aws'"
        />
        <x-input-error for="secret" />
    </x-field>

    <x-field class="max-w-sm">
        <x-label>{{ __('account.providers.name.label') }}</x-label>
        <x-inputs.text
            name="name"
            wire:model.defer="name"
            x-bind:disabled="provider !== 'aws'"
        />
        <x-help>{{ __('account.providers.name.help') }}</x-help>
        <x-input-error for="name" />
    </x-field>

</div>
