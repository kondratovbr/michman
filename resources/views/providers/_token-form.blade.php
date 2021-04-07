<div class="space-y-4">

    <x-message colors="info">
        <x-lang key="providers.digital-ocean-info" />
    </x-message>

    <x-field>
        <x-label>{{ __('account.providers.token.label') }}</x-label>
        <x-inputs.text name="token" />
    </x-field>

    <x-field>
        <x-label>{{ __('account.providers.name.label') }}</x-label>
        <x-inputs.text name="name" />
        <x-help>{{ __('account.providers.name.help') }}</x-help>
    </x-field>

</div>
