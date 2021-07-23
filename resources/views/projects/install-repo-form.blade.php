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
                            name="state.vcsProviderKey"
                            value="{{ $provider->getKey() }}"
                            wire:model="state.vcsProviderKey"
                        >
                            <x-slot name="content">
                                <x-icon size="16"><i class="{{ config("vcs.list.{$provider->provider}.icon") }} fa-3x"></i></x-icon>
                                <span class="mt-1">{{ __("projects.repo.providers.{$provider->provider}") }}</span>
                            </x-slot>
                        </x-radio-card>
                    @endforeach
                </x-radio-cards>
            </x-field>

            @isset($state['vcsProviderKey'])

                <x-field class="max-w-sm">
                    <x-label>{{ __('projects.repo.configure.repo.label') }}</x-label>
                    <x-inputs.text
                        name="state.repo"
                        wire:model.defer="state.repo"
                        placeholder="user/repository"
                    />
                    <x-input-error for="state.repo" />
                </x-field>

                <x-field class="max-w-sm">
                    <x-label>{{ __('projects.repo.configure.branch.label') }}</x-label>
                    <x-inputs.text
                        name="state.branch"
                        wire:model.defer="state.branch"
                        placeholder="master"
                    />
                    <x-input-error for="state.branch" />
                </x-field>

                <x-field>
                    <x-checkbox-new
                        name="state.installDependencies"
                        wire:model="state.installDependencies"
                    >
                        {{ __('projects.repo.configure.install-dependencies.label') }}
                    </x-checkbox-new>
                    <x-input-error for="installDependencies" />
                </x-field>

    {{--            TODO: CRITICAL! This whole thing needs some explanation and a link to docs. Don't forget to provide explanation on where to actually add those deploy keys inside the repo page for all providers.--}}
                <div class="space-y-3">
                    <x-field>
                        <x-checkbox-new
                            name="state.useDeployKey"
                            wire:model="state.useDeployKey"
                        >
                            {{ __('projects.repo.configure.use-deploy-key.label') }}
                        </x-checkbox-new>
                        <x-input-error for="useDeployKey" />
                    </x-field>

                    @if($state['useDeployKey'])
                        <x-message colors="info">
                            <p class="max-w-prose">
                                {{ __('projects.repo.configure.use-deploy-key.enabled-message',
                                ['provider' => __("projects.repo.providers.{$provider->provider}")]) }}
                            </p>
    {{--                            TODO: Make a "Copy" button to copy the key to clipboard quickly. --}}
                            <x-code-block class="mt-3" :wrap="true">{{ $project->deploySshKey->publicKeyString }}</x-code-block>
                        </x-message>
                    @else
                        <x-message colors="info">
                            <p class="max-w-prose">
                                {{ __('projects.repo.configure.use-deploy-key.disabled-message',
                                    ['provider' => __("projects.repo.providers.{$provider->provider}")]) }}
                            </p>
                        </x-message>
                    @endif
                </div>

            @endisset

        </div>
    </x-slot>

    <x-slot name="actions">
        <div class="flex items-center space-x-3">
            <x-buttons.secondary
                wire:click.prevent="resetState"
            >{{ __('buttons.cancel') }}</x-buttons.secondary>
            <x-buttons.primary
                wire:click.prevent="update"
                wire:loading.attr="disabled"
            >{{ __('projects.repo.configure.button') }}</x-buttons.primary>
        </div>
    </x-slot>

</x-form-section>