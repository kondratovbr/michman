{{--TODO: IMPORTANT! The form desperately needs some loading indicators - it may take some time to work.--}}
{{--TODO: Make a "What Will Be Installed" type of list somewhere here after a server type is chosen.--}}

<div class="space-y-6">

    <x-field wire:key="provider_id">
        <x-label>API Credentials</x-label>
        <x-select
            :options="$providers"
            :default="isset($state['provider_id'])"
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
            <p class="max-w-prose">Something went wrong while calling DigitalOcean API.</p>
            <p class="max-w-prose">DigitalOcean API error code: {{ $apiErrorCode }}</p>
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
                    :default="isset($state['region'])"
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
                        :default="isset($state['size'])"
                        name="size"
                        wire:model="state.size"
                        wire:key="search-select-size-{{ $state['region'] }}"
                        placeholder="Select size"
                    />
                </x-field>

                @isset($state['size'])
                    <x-field>
                        <x-label>Type</x-label>
                        <x-select
                            :options="$types"
                            :default="true"
                            name="type"
                            wire:model="state.type"
                            wire:key="select-type-{{ $state['size'] }}"
                            placeholder="Select server type"
                        />
                        <x-message class="mt-3">
                            <div class="max-w-prose space-y-3">
                                <p>
                                    Application servers include everything you need to deploy your Python / Django application.
                                    If you don't want to install a database, you may disable its installation below.
                                </p>
                                <p>The following will be installed on the server:</p>
                                <ul class="list-disc list-outside ml-3 ">
                                    @foreach(config('servers.types.' . $state['type'] . '.install') as $program)
                                        <li>{{ __('servers.programs.' . $program) }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </x-message>
                    </x-field>
                @endisset
            @endisset
        @endisset
    @endisset

</div>
