<x-action-section>
    <x-slot name="title">
        {{ __('projects.repo.configure.title') }}
    </x-slot>

    <x-slot name="content">
        <p>{{ __('projects.repo.configure.no-vcs-provider') }}</p>

        <x-buttons.primary
            class="mt-5"
            :link="true"
            href="/account/vcs"
        >{{ __('projects.repo.configure.no-vcs-provider-button') }}</x-buttons.primary>
    </x-slot>
</x-action-section>
