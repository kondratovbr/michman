<x-form-section submit="store">

    <x-slot name="title">
        New provider
    </x-slot>

    <x-slot name="description">
        Add a new server provider API credentials.
    </x-slot>

    <x-slot name="form">
        <div class="space-y-4">

            <span>Server Provider</span>

            {{-- Check-cards container --}}
            <div class="flex space-x-6">

                <x-radio-card name="provider" value="aws" checked>
                    <x-slot name="content">
                        <x-icon size="16"><i class="fab fa-aws fa-3x"></i></x-icon>
                        <span class="mt-1">AWS</span>
                    </x-slot>
                </x-radio-card>

                <x-radio-card name="provider" value="digital-ocean" checked>
                    <x-slot name="content">
                        <x-icon size="16"><i class="fab fa-digital-ocean fa-3x"></i></x-icon>
                        <span class="mt-1">DigitalOcean</span>
                    </x-slot>
                </x-radio-card>

                <x-radio-card name="provider" value="linode">
                    <x-slot name="content">
                        <x-icon size="16"><i class="fab fa-linode fa-3x"></i></x-icon>
                        <span class="mt-1">Linode</span>
                    </x-slot>
                </x-radio-card>

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
