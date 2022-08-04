{{--TODO: IMPORTANT! The form desperately needs some loading indicators - it may take some time to work.--}}

<div class="space-y-6">

    <x-field wire:key="provider_id">
        <x-label>{{ __('servers.create.credentials') }}</x-label>
        <x-select
            name="state.provider_id"
            :options="$providers"
            :default="isset($state['provider_id'])"
            wire:model="state.provider_id"
            placeholder="Select API credentials"
        />
        <x-input-error for="state.provider_id" />
    </x-field>

    {{-- Gracefully handle possible external API errors. --}}
    @isset($apiErrorCode)
{{--        TODO: IMPORTANT! Make this more concise and detailed, add more explanations for different erorr codes and add a link/button to contact suport.--}}
        <x-message colors="danger">
            <p class="max-w-prose">{{ __('servers.create.digital-ocean.something-wrong') }}</p>
            <p class="max-w-prose">{{ __('servers.create.digital-ocean.error-code', ['code' => $apiErrorCode]) }}</p>
        </x-message>
    @else
{{--        TODO: Don't forget to add an explanation here. Not everyone knows where the name will be used and even WTF is it.--}}
        <x-field>
            <x-label>{{ __('servers.create.name') }}</x-label>
            <x-inputs.text
                name="state.name"
                wire:model="state.name"
                required
            />
            <x-input-error for="state.name" />
        </x-field>

        @isset($state['provider_id'])
            <x-field>
                <x-label>{{ __('servers.create.region') }}</x-label>
                <x-search-select
                    name="state.region"
                    :options="$availableRegions"
                    :default="isset($state['region'])"
                    wire:model="state.region"
                    placeholder="Select region"
                />
                <x-input-error for="state.region" />
            </x-field>

            @isset($state['region'])
                <x-field>
                    <x-label>{{ __('servers.create.size') }}</x-label>
                    <x-search-select
                        name="state.size"
                        :options="$availableSizes"
                        :default="isset($state['size'])"
                        wire:model="state.size"
                        placeholder="Select size"
                    />
                    <x-input-error for="state.size" />
                </x-field>

                @isset($state['size'])
                    <x-field>
                        <x-label>{{ __('servers.create.type') }}</x-label>
                        <x-select
                            name="state.type"
                            :options="$types"
                            :default="isset($state['type'])"
                            wire:model="state.type"
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
                                <x-label>{{ __('servers.create.python-version') }}</x-label>
                                <x-select
                                    name="state.python_version"
                                    :options="$pythonVersions"
                                    :default="isset($state['python_version'])"
                                    wire:model="state.python_version"
                                />
                                <x-input-error for="state.python_version" />
                            </x-field>
                        @endif
                        @if($this->shouldInstall('database'))
                            <x-field>
                                <x-label>{{ __('servers.create.database') }}</x-label>
                                <x-select
                                    name="state.database"
                                    :options="$databases"
                                    :default="isset($state['database'])"
                                    wire:model="state.database"
                                />
                                <x-input-error for="state.database" />
                            </x-field>
                        @endif
                        @if($this->shouldInstall('cache'))
                            <x-field>
                                <x-label>{{ __('servers.create.cache') }}</x-label>
                                <x-select
                                    name="state.cache"
                                    :options="$caches"
                                    :default="isset($state['cache'])"
                                    wire:model="state.cache"
                                />
                                <x-input-error for="state.cache" />
                            </x-field>
                        @endif
                    @endisset
                @endisset
            @endisset

        @endisset
    @endisset

    <x-message colors="info">
        <div class="max-w-prose space-y-3">
            <p>{{ __('servers.create.will-be-installed') }}</p>
            <ul class="list-disc list-outside ml-3 ">
                @foreach(config('servers.types.' . $state['type'] . '.install') ?? [] as $program)
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

    <x-modals.dialog wire:model="successModalOpen">
        <x-slot name="header">
            {{ __('servers.create.modal.title') }}
        </x-slot>

        <x-slot name="content">
            <p>{{ __('servers.create.modal.explanation-1') }}</p>
            <p class="mt-3">{{ __('servers.create.modal.explanation-2') }}</p>
            <span class="mt-3">{{ __('servers.create.modal.sudo-password') }}</span>
            <x-copy-code-block class="mt-1">{{ optional($server)->sudoPassword }}</x-copy-code-block>
            @if(! empty($server->databaseRootPassword))
                <span class="mt-3">{{ __('servers.create.modal.db-password') }}</span>
                <x-copy-code-block class="mt-1">{{ optional($server)->databaseRootPassword }}</x-copy-code-block>
            @endif
        </x-slot>

        <x-slot name="actions">
            <x-buttons.secondary
                x-on:click.prevent="show = false"
                wire:loading.attr="disabled"
            >
                {{ __('buttons.close') }}
            </x-buttons.secondary>
        </x-slot>
    </x-modals.dialog>

</div>
