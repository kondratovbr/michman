{{--TODO: IMPORTANT! The thing should show a message and a button/link to add a provider if the user has none.--}}
{{--TODO: Can this form be made into a more pleasant multi-step process? Check out ploi.io, maybe they have a better proccess. Maybe also check out DigitalOcean and Linode and others - maybe they have a better example of this process?--}}
{{--TODO: Maybe I can somehow figure out how to pre-cache data from a third-party API when user opens this page, so it works a bit faster?--}}

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
                    class="flex flex-wrap space-x-6-right space-y-6-bottom -mb-5"
                >
                    @foreach(config('providers.list') as $providerName => $providerConfig)
                        <x-radio-card
                            class="h-32 w-32"
                            name="provider"
                            value="{{ $providerName }}"
                            wire:model="provider"
                            :disabled="! Arr::hasValue($availableProviders, $providerName)"
                        >
                            <x-slot name="content">
                                <x-icon size="16"><i class="{{ $providerConfig['icon'] }} fa-3x"></i></x-icon>
                                <span class="mt-1">{{ __('account.providers.' . $providerName . '.name') }}</span>
                            </x-slot>
                        </x-radio-card>
                    @endforeach
                </div>
                <x-input-error for="provider" />
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
        <div class="w-full flex justify-between items-center">
            <div class="space-x-3">
                <x-buttons.primary
                    wire:click.prevent="store"
                    wire:loading.attr="disabled"
                >{{ __('servers.create.button') }}</x-buttons.primary>
                <x-buttons.secondary
                    wire:click.prevent="cancel"
                    wire:loading.attr="disabled"
                >{{ __('buttons.cancel') }}</x-buttons.secondary>
            </div>
            <div wire:loading><x-spinner/></div>
        </div>
    </x-slot>

</x-form-section>
