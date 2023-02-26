@if($completedSteps < $totalSteps)

<x-action-section>
    <x-slot name="title">
        {{ __('onboarding.title') }} ({{ $completedSteps }}/{{ $totalSteps }})
    </x-slot>

    <div class="divide-y divide-navy-100 rounded-lg shadow relative z-10 mb-6">
        <ul class="divide-y divide-navy-100">

            <x-onboarding.step step="1" :completed="$steps[1]['completed']">
                <x-slot name="title">{{ __('onboarding.steps.1.title') }}</x-slot>
                <x-slot name="subtitle">{{ __('onboarding.steps.1.subtitle') }}</x-slot>

                <x-buttons.secondary :link="true" href="{{ route('account.show', 'vcs') }}">
                    {{ __('onboarding.steps.1.button') }}
                </x-buttons.secondary>
            </x-onboarding.step>

            <x-onboarding.step step="2" :completed="$steps[2]['completed']">
                <x-slot name="title">{{ __('onboarding.steps.2.title') }}</x-slot>
                <x-slot name="subtitle">{{ __('onboarding.steps.2.subtitle') }}</x-slot>

                <x-buttons.secondary :link="true" href="{{ route('account.show', 'providers') }}">
                    {{ __('onboarding.steps.2.button') }}
                </x-buttons.secondary>
            </x-onboarding.step>

            <x-onboarding.step step="3" :completed="$steps[3]['completed']">
                <x-slot name="title">{{ __('onboarding.steps.3.title') }}</x-slot>
                <x-slot name="subtitle">{{ __('onboarding.steps.3.subtitle') }}</x-slot>
            </x-onboarding.step>

            <x-onboarding.step step="4" :completed="$steps[4]['completed']">
                <x-slot name="title">{{ __('onboarding.steps.4.title') }}</x-slot>
                <x-slot name="subtitle">{{ __('onboarding.steps.4.subtitle') }}</x-slot>

                @if(isset($steps[4]['server_id']))
                    <x-buttons.secondary :link="true" href="{{ route('servers.show', $steps[4]['server_id']) }}">
                        {{ __('onboarding.steps.4.button') }}
                    </x-buttons.secondary>
                @endunless
            </x-onboarding.step>

            <x-onboarding.step step="5" :completed="$steps[5]['completed']">
                <x-slot name="title">{{ __('onboarding.steps.5.title') }}</x-slot>
                <x-slot name="subtitle">{{ __('onboarding.steps.5.subtitle') }}</x-slot>

                @if(isset($steps[5]['project_id']))
                    <x-buttons.secondary :link="true" href="{{ route('projects.show', $steps[5]['project_id']) }}">
                        {{ __('onboarding.steps.5.button') }}
                    </x-buttons.secondary>
                @endunless
            </x-onboarding.step>

            <x-onboarding.step step="6" :completed="$steps[6]['completed']">
                <x-slot name="title">{{ __('onboarding.steps.6.title') }}</x-slot>
                <x-slot name="subtitle">{{ __('onboarding.steps.6.subtitle') }}</x-slot>

                <x-buttons.secondary :link="true" href="/billing">
                    {{ __('onboarding.steps.6.button') }}
                </x-buttons.secondary>
            </x-onboarding.step>

        </ul>

        <div
            class="border-primary-100 absolute inset-y-0 left-8 -ml-px border-l-2 border-dashed"
            style="z-index: -1;"
        ></div>
    </div>

</x-action-section>

@endif
