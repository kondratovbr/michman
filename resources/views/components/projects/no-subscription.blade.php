<x-action-section>
    <x-slot name="title">
        {{ __('projects.create.title') }}
    </x-slot>

    <x-slot name="content">
        <p>{{ __('projects.create.no-subscription') }}</p>

        <x-buttons.primary
            class="mt-5"
            :link="true"
            href="/billing"
        >{{ __('billing.upgrade-button') }}</x-buttons.primary>
    </x-slot>
</x-action-section>
