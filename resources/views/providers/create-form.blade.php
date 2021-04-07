{{--TODO: Don't forget to update this for mobile. Radio-cards should definitely be smaller, for example.--}}

<x-form-section submit="store">

    <x-slot name="title">
        {{ __('account.providers.create.title') }}
    </x-slot>

    <x-slot name="description">
        {{ __('account.providers.create.description') }}
    </x-slot>

    <x-slot name="form">
        <div class="space-y-6">

            <x-field class="space-y-2">
                <div>{{ __('account.providers.provider.label') }}</div>

                {{-- Check-cards container --}}
                <div class="flex space-x-6">

                    <x-radio-card class="h-32 w-32" name="provider" value="digital-ocean" checked>
                        <x-slot name="content">
                            <x-icon size="16"><i class="fab fa-digital-ocean fa-3x"></i></x-icon>
                            <span class="mt-1">DigitalOcean</span>
                        </x-slot>
                    </x-radio-card>

                    <x-radio-card class="h-32 w-32" name="provider" value="aws">
                        <x-slot name="content">
                            <x-icon size="16"><i class="fab fa-aws fa-3x"></i></x-icon>
                            <span class="mt-1">AWS</span>
                        </x-slot>
                    </x-radio-card>

                    <x-radio-card class="h-32 w-32" name="provider" value="linode">
                        <x-slot name="content">
                            <x-icon size="16"><i class="fab fa-linode fa-3x"></i></x-icon>
                            <span class="mt-1">Linode</span>
                        </x-slot>
                    </x-radio-card>

                </div>
            </x-field>

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
