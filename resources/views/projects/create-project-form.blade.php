{{--TODO: CRITICAL! Unfinished? Need loading animations, etc.--}}

{{--TODO: CRITICAL! Have a default project created and deployed with every suitable new server, like Forge does - to have more mentions of us on the internet.--}}

<x-form-section submit="store">

    <x-slot name="title">{{ __('projects.create.title') }}</x-slot>

    <x-slot name="form">
        <div class="space-y-6">

            <x-field class="max-w-sm">
                <x-label>{{ __('projects.create.form.domain.label') }}</x-label>
                <x-inputs.text
                    name="domain"
                    wire:model.defer="domain"
                    placeholder="example.com"
                />
                <x-input-error for="domain" />
            </x-field>

            <x-field>
                <x-label>{{ __('projects.create.form.aliases.label') }}</x-label>
                <x-inputs.text
                    name="aliases"
                    wire:model.defer="aliases"
                    placeholder="another.net, one-more-time.biz"
                />
                <x-input-error for="domain" />
                <x-help>{{ __('projects.create.form.aliases.help') }}</x-help>
            </x-field>

            <x-field class="max-w-sm">
                <x-label>{{ __('projects.create.form.type.label') }}</x-label>
                <x-select
                    name="type"
                    :options="$types"
                    :default="true"
                    wire:model="type"
                    wire:key="select-type"
                />
                <x-input-error for="type" />
            </x-field>

            <x-field class="max-w-sm">
                <x-label>{{ __('projects.create.form.root.label') }}</x-label>
                <x-inputs.text
                    name="root"
                    wire:model.defer="root"
                />
                <x-input-error for="root" />
                <x-help><x-lang key="projects.root-help" /></x-help>
            </x-field>

            <x-field class="max-w-sm">
                <x-label>{{ __('projects.create.form.python-version.label') }}</x-label>
                <x-select
                    name="python_version"
                    :options="$pythonVersions"
                    :default="true"
                    wire:model="python_version"
                    wire:key="select-python-version"
                />
                <x-input-error for="python_version" />
            </x-field>

            <x-field>
                <x-checkbox-new
                    name="allow_sub_domains"
                    wire:model="allow_sub_domains"
                >
                    {{ __('projects.create.form.allow-sub-domains.label') }}
                </x-checkbox-new>
                <x-input-error for="allow_sub_domains" />
            </x-field>

            @if($server->canCreateDatabase())

                <div
                    class="space-y-6"
                    x-data="{ createDatabase: false }"
                >

                    <x-field>
                        <x-checkbox-new
                            name="create_database"
                            wire:model="create_database"
                            x-model="createDatabase"
                        >
                            {{ __('projects.create.form.create-database.label') }}
                        </x-checkbox-new>
                        <x-input-error for="create_database" />
                    </x-field>

                    <x-field
                        class="max-w-sm"
                        x-show="createDatabase"
                        x-cloak
                    >
                        <x-label>{{ __('projects.create.form.db-name.label') }}</x-label>
                        <x-inputs.text
                            name="db_name"
                            wire:model.defer="db_name"
                        />
                        <x-input-error for="db_name" />
                    </x-field>

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
