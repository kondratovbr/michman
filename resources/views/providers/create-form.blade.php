{{--TODO: Don't forget to update this for mobile. Radio-cards should definitely be smaller, for example.--}}

<x-form-section submit="store">

    <x-slot name="title">
        {{ __('account.providers.create.title') }}
    </x-slot>

    <x-slot name="description">
        {{ __('account.providers.create.description') }}
    </x-slot>

    <x-slot name="form">
        <div
            class="space-y-6"
            x-data="{ formType: 'token', provider: '{{ config('providers.default') }}' }"
        >

            <x-field class="space-y-2">
                <div>{{ __('account.providers.provider.label') }}</div>

                {{-- Check-cards container --}}
                <div class="flex space-x-6">

                    @foreach(config('providers.list') as $providerName => $providerConfig)
                        <x-radio-card
                            class="h-32 w-32"
                            name="provider"
                            value="{{ $providerName }}"
                            wire:model="provider"
                            x-model="provider"
{{--                            :disabled="(bool) $providerConfig['disabled']"--}}
                            x-on:click="formType = '{{ $providerConfig['auth_type'] }}'"
                        >
                            <x-slot name="content">
                                <x-icon size="16"><i class="{{ $providerConfig['icon'] }} fa-3x"></i></x-icon>
                                <span class="mt-1">{{ __('account.providers.' . $providerName . '.name') }}</span>
                            </x-slot>
                        </x-radio-card>
                    @endforeach

                </div>
            </x-field>

            <div class="space-y-4">

{{--                TODO: Don't forget to add similar helpful messages for other providers.--}}
{{--                TODO: Is it possible to DRY this ridiculous Alpine transition declaration?--}}
                <x-message
                    class="max-w-prose"
                    colors="info"
                    x-show.transition.in.duration.300ms.origin.top.opacity.scale.95="provider === 'digital_ocean_v2'"
                    x-cloak
                >
                    <x-lang key="providers.digital-ocean-info" />
                </x-message>

                <x-field
                    x-show.transition.in.duration.300ms.origin.top.opacity.scale.95="formType === 'token'"
                    x-cloak
                >
                    <x-label>{{ __('account.providers.token.label') }}</x-label>
                    <x-inputs.text
                        name="token"
                        wire:model.defer="token"
                    />
                </x-field>

                <x-field
                    x-show.transition.in.duration.300ms.origin.top.opacity.scale.95="formType === 'key-secret'"
                    x-cloak
                >
                    <x-label>{{ __('account.providers.key.label') }}</x-label>
                    <x-inputs.text
                        name="key"
                        wire:model.defer="key"
                    />
                </x-field>

                <x-field
                    x-show.transition.in.duration.300ms.origin.top.opacity.scale.95="formType === 'key-secret'"
                    x-cloak
                >
                    <x-label>{{ __('account.providers.secret.label') }}</x-label>
                    <x-inputs.text
                        name="secret"
                        wire:model.defer="secret"
                    />
                </x-field>

                <x-field>
                    <x-label>{{ __('account.providers.name.label') }}</x-label>
                    <x-inputs.text
                        name="name"
                        wire:model.defer="name"
                    />
                    <x-help>{{ __('account.providers.name.help') }}</x-help>
                </x-field>

            </div>
        </div>
    </x-slot>

    <x-slot name="actions">
        <div class="flex items-center space-x-3">
            <x-buttons.primary>
                {{ __('buttons.save') }}
            </x-buttons.primary>
            <x-action-message on="saved">
                {{ __('misc.saved') }}
            </x-action-message>
        </div>
    </x-slot>

</x-form-section>
