{{--TODO: CRITICAL! Unfinished. Need loading animations, etc. Forge does loading very simple - by a spinner on the primary button that shows any time the component is loading, like "wire:loading" stuff.--}}

<x-form-section submit="store">

    <x-slot name="title">{{ __('projects.repo.configure.title') }}</x-slot>

    <x-slot name="form">
        <div class="space-y-6">

            <x-field>
                <x-radio-cards>
                    @foreach($vcsProviders as $provider)
                        <x-radio-card
                            class="h-32 w-32"
                            name="vcsProviderKey"
                            value="{{ $provider->getKey() }}"
                            wire:model="vcsProviderKey"
                        >
                            <x-slot name="content">
                                <x-icon size="16"><i class="{{ config("vcs.list.{$provider->provider}.icon") }} fa-3x"></i></x-icon>
                                <span class="mt-1">{{ __("projects.repo.providers.{$provider->provider}") }}</span>
                            </x-slot>
                        </x-radio-card>
                    @endforeach
                </x-radio-cards>
            </x-field>

            <x-field class="max-w-sm">
                <x-label>{{ __('projects.repo.configure.repo.label') }}</x-label>
                <x-inputs.text
                    name="repo"
                    wire:model.defer="repo"
                    placeholder="user/repository"
                />
                <x-input-error for="repo" />
            </x-field>

            <x-field class="max-w-sm">
                <x-label>{{ __('projects.repo.configure.branch.label') }}</x-label>
                <x-inputs.text
                    name="branch"
                    wire:model.defer="branch"
                    placeholder="master"
                />
                <x-input-error for="branch" />
            </x-field>

            <x-field>
                <x-checkbox-new
                    name="installDependencies"
                    wire:model="installDependencies"
                >
                    {{ __('projects.repo.configure.install-dependencies.label') }}
                </x-checkbox-new>
                <x-input-error for="installDependencies" />
            </x-field>

{{--            TODO: CRITICAL! This whole thing needs some explanation and a link to docs. Don't forget to provide explanation on where to actually add those deploy keys inside the repo page for all providers.--}}
            <x-field>
                <x-checkbox-new
                    name="useDeployKey"
                    wire:model="useDeployKey"
                >
                    {{ __('projects.repo.configure.use-deploy-key.label') }}
                </x-checkbox-new>
                <x-input-error for="useDeployKey" />
            </x-field>

            @if($useDeployKey)
                <x-code-block>
                    //
                </x-code-block>
                <x-message colors="info">
                    <div class="max-w-prose space-y-3">
                        {{ __('projects.repo.configure.use-deploy-key.enabled-message',
                            ['provider' => __("projects.repo.providers.{$provider->provider}")]) }}
                    </div>
                </x-message>
            @else
                <x-message colors="info">
                    <div class="max-w-prose space-y-3">
                        {{ __('projects.repo.configure.use-deploy-key.disabled-message',
                            ['provider' => __("projects.repo.providers.{$provider->provider}")]) }}
                    </div>
                </x-message>
            @endif

        </div>
    </x-slot>

    <x-slot name="actions">
        <x-buttons.primary
            wire:click.prevent="update"
            wire:loading.attr="disabled"
        >
            {{ __('projects.repo.configure.button') }}
        </x-buttons.primary>
    </x-slot>

</x-form-section>
