{{--TODO: CRITICAL! Unfinished? Need loading animations, etc.--}}

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
            </x-field>

            <x-field class="max-w-sm">
                <x-label>{{ __('projects.create.form.python-version.label') }}</x-label>
                <x-select
                    name="pythonVersion"
                    :options="$pythonVersions"
                    :default="true"
                    wire:model="pythonVersion"
                    wire:key="select-python-version"
                />
                <x-input-error for="pythonVersion" />
            </x-field>

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
