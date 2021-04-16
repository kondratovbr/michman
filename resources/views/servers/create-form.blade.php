<x-form-section submit="store">

    <x-slot name="title">
        {{ __('servers.create.title') }}
    </x-slot>

    <x-slot name="form">

        <div class="space-y-6">

{{--            TODO: I'm using this field at least twice - see "providers.create-form". DRY?--}}
{{--            TODO: Add some loading animation here - loading an actual form may take some time. --}}
            <x-field>
                <div
                    class="flex space-x-6"
                    {{-- TODO: Do I even use Alpine here? --}}
                    x-data="{ provider: '' }"
                >
                    @foreach(config('providers.list') as $providerName => $providerConfig)
                        <x-radio-card
                            class="h-32 w-32"
                            name="provider"
                            value="{{ $providerName }}"
                            wire:model="provider"
                            x-model="provider"
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
        Actions!
    </x-slot>

</x-form-section>
