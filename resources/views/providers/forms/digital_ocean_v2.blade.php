<div class="space-y-4">

{{--    TODO: Don't forget to add similar helpful messages for other providers.--}}
{{--    TODO: Is it possible to DRY this ridiculous Alpine transition declaration?--}}
    <x-message class="max-w-prose" colors="info">
        <x-lang key="providers.digital-ocean-info" />
    </x-message>

    <x-field>
        <x-label>{{ __('account.providers.token.label-pat') }}</x-label>
        <x-inputs.text
            name="token"
            wire:model.defer="token"
            x-bind:disabled="provider !== 'digital_ocean_v2'"
        />
        <x-input-error for="token" />
    </x-field>

    <x-field>
        <x-label>{{ __('account.providers.name.label') }}</x-label>
        <x-inputs.text
            name="name"
            wire:model.defer="name"
            x-bind:disabled="provider !== 'digital_ocean_v2'"
        />
        <x-help>{{ __('account.providers.name.help') }}</x-help>
        <x-input-error for="name" />
    </x-field>

</div>
