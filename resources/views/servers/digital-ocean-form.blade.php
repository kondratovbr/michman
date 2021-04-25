{{--TODO: IMPORTANT! The form desperately needs some loading indicators - it may take some time to work.--}}
{{--TODO: Make a "What Will Be Installed" type of list somewhere here after a server type is chosen.--}}

<div class="space-y-6">

    <x-field wire:key="provider_id">
        <x-label>API Credentials</x-label>
        <x-select
            :options="$providers"
            :default="true"
            name="provider_id"
            wire:model="state.provider_id"
            wire:key="search-select-provider_id"
            placeholder="Select API credentials"
        />
    </x-field>

    {{-- Gracefully handle possible external API errors. --}}
    @isset($apiErrorCode)
        {{--TODO: IMPORTANT! Make this more concise and detailed, add more explanations for different erorr codes and add a link/button to contact suport.--}}
        <x-message colors="danger">
            <p>Something went wrong while calling DigitalOcean API.</p>
            <p>DigitalOcean API error code: {{ $apiErrorCode }}</p>
        </x-message>
    @else
{{--        TODO: Don't forget to add an explanation here. Not everyone knows where the name will be used and even WTF is it. --}}
        <x-field>
            <x-label>Name</x-label>
            <x-inputs.text
                name="name"
                wire:model="state.name"
            />
        </x-field>

        @isset($state['provider_id'])
            <x-field>
                <x-label>Region</x-label>
                <x-search-select
                    :options="$availableRegions"
                    name="region"
                    wire:model="state.region"
                    wire:key="search-select-region-{{ $state['provider_id'] }}"
                    placeholder="Select region"
                />
            </x-field>

            @isset($state['region'])
                <x-field>
                    <x-label>Size</x-label>
                    <x-search-select
                        :options="$availableSizes"
                        name="size"
                        wire:model="state.size"
                        wire:key="search-select-size-{{ $state['region'] }}"
                        placeholder="Select size"
                    />
                </x-field>
            @endisset
        @endisset
    @endisset

</div>
