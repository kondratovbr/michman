<x-action-section>
    <x-slot name="title">{{ __('servers.manage.server-key.title') }}</x-slot>
    <x-slot name="description">{{ __('servers.manage.server-key.description') }}</x-slot>

    <x-slot name="content">
        <x-message>{{ __('servers.manage.server-key.info') }}</x-message>

        <x-copy-code-block class="mt-6" :wrap="true">
            {!! $server->serverSshKey->publicKeyString !!}
        </x-copy-code-block>
    </x-slot>
</x-action-section>

<x-section-separator/>

<x-action-section>
    <x-slot name="title">{{ __('servers.manage.worker-key.title') }}</x-slot>
    <x-slot name="description">{{ __('servers.manage.worker-key.description') }}</x-slot>

    <x-slot name="content">
{{--        TODO: The code blocks look especially mushed here. Should redesign the code block component.--}}
        <x-message><x-lang key="servers.worker-key-info" /></x-message>

        <x-copy-code-block class="mt-6" :wrap="true">
            {!! $server->workerSshKey->publicKeyString !!}
        </x-copy-code-block>
    </x-slot>
</x-action-section>

<x-section-separator/>

<livewire:servers.delete-server-form :server="$server" />
