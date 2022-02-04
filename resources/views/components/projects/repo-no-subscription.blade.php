<x-action-section>
    <x-slot name="title">
        {{ __('projects.repo.configure.title') }}
    </x-slot>

    <x-slot name="content">
        <p>{{ __('projects.repo.configure.no-subscription') }}</p>

        <x-buttons.primary
            class="mt-5"
            :link="true"
            href="/billing"
        >{{ __('billing.upgrade-button') }}</x-buttons.primary>
    </x-slot>
</x-action-section>
