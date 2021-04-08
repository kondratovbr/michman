<div class="space-y-4">

    <x-field>
        <x-label>{{ __('account.providers.token.label-token') }}</x-label>
        <x-inputs.text
            name="token"
            wire:model.defer="token"
            x-bind:disabled="provider !== 'linode'"
        />
        <x-input-error for="token" />
    </x-field>

    <x-field>
        <x-label>{{ __('account.providers.name.label') }}</x-label>
        <x-inputs.text
            name="name"
            wire:model.defer="name"
            x-bind:disabled="provider !== 'linode'"
        />
        <x-help>{{ __('account.providers.name.help') }}</x-help>
        <x-input-error for="name" />
    </x-field>

</div>
