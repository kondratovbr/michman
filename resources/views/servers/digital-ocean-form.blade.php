{{--TODO: IMPORTANT! The form desperately needs some loading indicators - it may take some time to work.--}}
{{--TODO: Make a "What Will Be Installed" type of list somewhere here after a server type is chosen.--}}
{{--TODO: Shoud I add DigitalOcean weekly backups feature? This way of deploying kinda doesn't need them - we need to backup databases, of course, and uploaded files somehow as well. Does DO has some other backup options? Snapshots? So, maybe we should offer to enable something better here?--}}

<div class="space-y-6">

    <x-field wire:key="provider_id">
        <x-label>API Credentials</x-label>
        <x-select
            name="state.provider_id"
            :options="$providers"
            :default="isset($state['provider_id'])"
            wire:model="state.provider_id"
            wire:key="search-select-provider_id"
            placeholder="Select API credentials"
        />
        <x-input-error for="state.provider_id" />
    </x-field>

    {{-- Gracefully handle possible external API errors. --}}
    @isset($apiErrorCode)
{{--        TODO: IMPORTANT! Make this more concise and detailed, add more explanations for different erorr codes and add a link/button to contact suport.--}}
        <x-message colors="danger">
            <p class="max-w-prose">Something went wrong while calling DigitalOcean API.</p>
            <p class="max-w-prose">DigitalOcean API error code: {{ $apiErrorCode }}</p>
        </x-message>
    @else
{{--        TODO: Don't forget to add an explanation here. Not everyone knows where the name will be used and even WTF is it. --}}
        <x-field>
            <x-label>Name</x-label>
            <x-inputs.text
                name="state.name"
                wire:model="state.name"
                required
            />
            <x-input-error for="state.name" />
        </x-field>

        @isset($state['provider_id'])
            <x-field>
                <x-label>Region</x-label>
                <x-search-select
                    name="state.region"
                    :options="$availableRegions"
                    :default="isset($state['region'])"
                    wire:model="state.region"
                    wire:key="search-select-region-{{ $state['provider_id'] }}"
                    placeholder="Select region"
                />
                <x-input-error for="state.region" />
            </x-field>

            @isset($state['region'])
                <x-field>
                    <x-label>Size</x-label>
                    <x-search-select
                        name="state.size"
                        :options="$availableSizes"
                        :default="isset($state['size'])"
                        wire:model="state.size"
                        wire:key="search-select-size-{{ $state['region'] }}"
                        placeholder="Select size"
                    />
                    <x-input-error for="state.size" />
                </x-field>

                @isset($state['size'])
                    <x-field>
                        <x-label>Type</x-label>
                        <x-select
                            name="state.type"
                            :options="$types"
                            :default="isset($state['type'])"
                            wire:model="state.type"
                            wire:key="select-type-{{ $state['size'] }}"
                            placeholder="Select server type"
                        />
                        @error('state.type')
                            <x-input-error for="state.type" />
                        @else
                            <x-message class="mt-3" colors="info">
                                <p class="max-w-prose">{{ __('servers.types.' . $state['type'] . '.description') }}</p>
                            </x-message>
                        @enderror
                    </x-field>

                    @isset($state['type'])
                        @if($this->shouldInstall('python'))
                            <x-field>
                                <x-label>Python Version</x-label>
                                <x-select
                                    name="state.python_version"
                                    :options="$pythonVersions"
                                    :default="isset($state['python_version'])"
                                    wire:model="state.python_version"
                                    wire:key="select-python_version-{{ $state['type'] }}"
                                />
                                <x-input-error for="state.python_version" />
                            </x-field>
                        @endif
                        @if($this->shouldInstall('database'))
                            <x-field>
                                <x-label>Database</x-label>
                                <x-select
                                    name="state.database"
                                    :options="$databases"
                                    :default="isset($state['database'])"
                                    wire:model="state.database"
                                    wire:key="select-database-{{ $state['type'] }}"
                                />
                                <x-input-error for="state.database" />
                            </x-field>
                            @if($state['database'] !== 'none')
                                <x-field>
                                    <x-label>Database Name</x-label>
                                    <x-inputs.text
                                        name="state.db_name"
                                        wire:model="state.db_name"
                                    />
                                    <x-input-error for="state.db_name" />
                                </x-field>
                            @endif
                        @endif
                        @if($this->shouldInstall('cache'))
                            <x-field>
                                <x-label>Cache</x-label>
                                <x-select
                                    name="state.cache"
                                    :options="$caches"
                                    :default="isset($state['cache'])"
                                    wire:model="state.cache"
                                    wire:key="select-cache-{{ $state['type'] }}"
                                />
                                <x-input-error for="state.cache" />
                            </x-field>
                        @endif
                    @endisset
                @endisset
            @endisset

            <div class="space-y-2 -mt-2">
{{--                TODO: This checkbox needs some explanatory text.--}}
                <x-field>
                    <x-checkbox-new
                        name="state.add_ssh_keys_to_vcs"
                        wire:model="state.add_ssh_keys_to_vcs"
                    >Add server's SSH key to source control providers</x-checkbox-new>
                </x-field>
            </div>

        @endisset
    @endisset

    <x-message colors="info">
        <div class="max-w-prose space-y-3">
            <p>The following will be installed on the server:</p>
            <ul class="list-disc list-outside ml-3 ">
                @foreach(config('servers.types.' . $state['type'] . '.install') as $program)
                    @if($program == 'database')
                        @if($state['database'] != 'none')
                            <li>{{ __('servers.databases.' . $state['database']) }}</li>
                        @endif
                    @elseif($program == 'python')
                        <li>{{ __('servers.programs.' . $program) . ' ' . Str::replace('_', '.', $state['python_version']) }}</li>
                    @elseif($program == 'cache')
                        <li>{{ __('servers.caches.' . $state['cache']) }}</li>
                    @else
                        <li>{{ __('servers.programs.' . $program) }}</li>
                    @endif
                @endforeach
            </ul>
        </div>
    </x-message>

</div>
