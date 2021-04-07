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

            @include('providers._token-form')

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
