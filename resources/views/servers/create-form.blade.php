{{--TODO: IMPORTANT! The thing should show a message and a button/link to add a provider if the user has none.--}}
{{--TODO: IMPORTANT! The "disabled" parameter should depend on if the user has a corresponding credentials added, not like now.--}}
{{--TODO: Can this form be made into a more pleasant multi-step process? Check out ploi.io, maybe they have a better proccess. Maybe also check out DigitalOcean and Linode and others - maybe they have a better example of this process?--}}

<x-form-section submit="store">

    <x-slot name="title">
        {{ __('servers.create.title') }}
    </x-slot>

    <x-slot name="form">

        <div class="space-y-6">

{{--            TODO: I'm using this field at least twice - see "providers.create-form". DRY?--}}
{{--            TODO: Add some loading animation here - loading an actual form may take some time due to external API calls. --}}
            <x-field>

                <div
                    {{-- Negative bottom margin compensates for the bottom margin on the elements. --}}
                    class="flex flex-wrap space-x-6-right space-y-6-bottom -mb-6"
                >
                    @foreach(config('providers.list') as $providerName => $providerConfig)
                        <x-radio-card
                            class="h-32 w-32"
                            name="provider"
                            value="{{ $providerName }}"
                            wire:model="provider"
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

            {{-- Just in case something goes wrong and there's no component we don't want to just crash. --}}
            @if($this->formComponent)
                {{-- We don't do it the tabs way using Alpine because we don't want to load everything
                     needed for every form - it may take several requests to third-party APIs.  --}}
                @livewire($this->formComponent)
            @endif

        </div>

    </x-slot>

    <x-slot name="actions">
        <x-buttons>
            <x-buttons.primary wire:click.prevent="store">{{ __('servers.create.button') }}</x-buttons.primary>
            <x-buttons.secondary wire:click.prevent="cancel">{{ __('buttons.cancel') }}</x-buttons.secondary>
        </x-buttons>
    </x-slot>

</x-form-section>
