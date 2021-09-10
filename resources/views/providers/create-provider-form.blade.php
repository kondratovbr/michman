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
            <x-message class="max-w-prose" colors="info">{{ __('account.providers.create.do-only') }}</x-message>

            <x-field>
                <x-label>{{ __('account.providers.provider.label') }}</x-label>
                {{-- Check-cards container --}}
                {{-- Negative bottom margin compensates for the bottom margin on the elements. --}}
                <div class="flex space-x-6-right space-y-6-bottom -mb-6">

                    @foreach(config('providers.list') as $providerName => $providerConfig)
                        <x-radio-card
                            class="h-32 w-32"
                            name="provider"
                            value="{{ $providerName }}"
                            wire:model.defer="provider"
                            x-model="provider"
                            x-on:click="formType = '{{ $providerConfig['auth_type'] }}'"
                            :disabled="(bool) $providerConfig['disabled']"
                        >
                            <x-slot name="content">
                                <x-icon size="16"><i class="{{ $providerConfig['icon'] }} fa-3x"></i></x-icon>
                                <span class="mt-1">{{ __('account.providers.' . $providerName . '.name') }}</span>
                            </x-slot>
                        </x-radio-card>
                    @endforeach

                </div>
            </x-field>

            <div
                x-show="provider === 'digital_ocean_v2'"
                x-transition.in.duration.300ms.origin.top.opacity.scale.95
                x-cloak
            >
                @include('providers.forms.digital_ocean_v2')
            </div>
            <div
                x-show="provider === 'aws'"
                x-transition.in.duration.300ms.origin.top.opacity.scale.95
                x-cloak
            >
                @include('providers.forms.aws')
            </div>
            <div
                x-show="provider === 'linode'"
                x-transition.in.duration.300ms.origin.top.opacity.scale.95
                x-cloak
            >
                @include('providers.forms.linode')
            </div>

        </div>
    </x-slot>

    <x-slot name="actions">
        <div class="flex items-center space-x-3">
            <x-buttons.primary>
                {{ __('account.providers.create.button') }}
            </x-buttons.primary>
            <x-action-message on="saved">
                {{ __('misc.saved') }}
            </x-action-message>
        </div>
    </x-slot>

</x-form-section>
