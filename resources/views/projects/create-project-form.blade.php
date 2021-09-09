{{--TODO: CRITICAL! Unfinished? Need loading animations, etc.--}}

{{--TODO: CRITICAL! Have a default project created and deployed with every suitable new server, like Forge does - to have more mentions of us on the internet.--}}

<x-form-section submit="store">

    <x-slot name="title">{{ __('projects.create.title') }}</x-slot>

    <x-slot name="form">
        <div class="space-y-6">

            <x-field class="max-w-sm">
                <x-label>{{ __('projects.create.form.domain.label') }}</x-label>
                <x-inputs.text
                    name="state.domain"
                    wire:model.defer="state.domain"
                    placeholder="example.com"
                />
                <x-input-error for="state.domain" />
            </x-field>

            <x-field>
                <x-label>{{ __('projects.create.form.aliases.label') }}</x-label>
                <x-inputs.text
                    name="state.aliases"
                    wire:model.defer="state.aliases"
                    placeholder="another.net, one-more-time.biz"
                />
                <x-input-error for="state.aliases" />
                <x-help>{{ __('projects.create.form.aliases.help') }}</x-help>
            </x-field>

            <x-field class="max-w-sm">
                <x-label>{{ __('projects.create.form.type.label') }}</x-label>
                <x-select
                    name="state.type"
                    :options="$types"
                    :default="true"
                    wire:model="state.type"
                    wire:key="select-type"
                />
                <x-input-error for="state.type" />
            </x-field>

            <x-field class="max-w-sm">
                <x-label>{{ __('projects.create.form.python-version.label') }}</x-label>
                <x-select
                    name="state.python_version"
                    :options="$pythonVersions"
                    :default="true"
                    wire:model="state.python_version"
                    wire:key="select-python-version"
                />
                <x-input-error for="state.python_version" />
            </x-field>

            <x-field>
                <x-checkbox-new
                    name="state.allow_sub_domains"
                    wire:model="state.allow_sub_domains"
                >
                    {{ __('projects.create.form.allow-sub-domains.label') }}
                </x-checkbox-new>
                <x-input-error for="state.allow_sub_domains" />
            </x-field>

            @if($server->canCreateDatabase())

                <div x-data="{ createDatabase: @entangle('state.create_database') }">

                    <x-field>
                        <x-checkbox-new
                            name="state.create_database"
                            x-model="createDatabase"
                        >
                            {{ __('projects.create.form.create-database.label') }}
                        </x-checkbox-new>
                        <x-input-error for="state.create_database" />
                    </x-field>

                    <div
                        class="mt-2 bg-navy-500 rounded-md space-y-6 py-4 px-4"
                        x-show="createDatabase"
                        x-cloak
                    >

                        <x-field class="max-w-sm">
                            <x-label>{{ __('projects.create.form.db-name.label') }}</x-label>
                            <x-inputs.text
                                name="state.db_name"
                                wire:model.defer="state.db_name"
                            />
                            <x-input-error for="state.db_name" />
                        </x-field>

                        <div x-data="{ createDbUser: @entangle('state.create_db_user') }">

                            <x-field>
                                <x-checkbox-new
                                    name="state.create_db_user"
                                    x-model="createDbUser"
                                >
                                    {{ __('projects.create.form.create-db-user.label') }}
                                </x-checkbox-new>
                                <x-input-error for="state.create_db_user" />
                            </x-field>

                            <div class="mt-3 space-y-2">

                                <x-field
                                    class="max-w-sm"
                                    x-bind:disabled="! createDbUser"
                                >
                                    <x-label>{{ __('projects.create.form.db-user-name.label') }}</x-label>
                                    <x-inputs.text
                                        name="state.db_user_name"
                                        wire:model.defer="state.db_user_name"
                                    />
                                    <x-input-error for="state.db_user_name" />
                                </x-field>

                                <x-field
                                    class="max-w-sm"
                                    x-bind:disabled="! createDbUser"
                                >
                                    <x-label>{{ __('projects.create.form.db-user-password.label') }}</x-label>
                                    <x-inputs.text
                                        name="state.db_user_password"
                                        wire:model.defer="state.db_user_password"
                                    />
                                    <x-input-error for="state.db_user_password" />
                                </x-field>

                            </div>

                        </div>

                    </div>

                </div>

            @endif

        </div>
    </x-slot>

    <x-slot name="actions">
        <x-buttons.primary
            wire:click.prevent="store"
            wire:loading.attr="disabled"
        >
            {{ __('projects.create.form.button') }}
        </x-buttons.primary>
    </x-slot>

</x-form-section>
