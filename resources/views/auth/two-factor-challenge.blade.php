{{--TODO: IMPORTANT! Unfinished!--}}

<x-layouts.guest>
    <x-auth-box>

        <div x-data="{ recovery: false }">
            <div class="mb-4" x-show="! recovery">
                {{ __('auth.tfa.please-confirm') }}
            </div>

            <div class="mb-4" x-show="recovery">
                {{ __('auth.tfa.please-confirm-recovery') }}
            </div>

            <x-validation-errors class="mb-4" />

            <x-form method="POST" action="{{ route('two-factor.login') }}">

                <x-field
                    class="mt-4"
                    x-show="!recovery"
                >
                    <x-label for="code">{{ __('forms.tfa.label') }}</x-label>
                    <x-inputs.tfa-code
                        name="code"
                        x-ref="code"
                        autofocus
                        x-bind:required="!recovery"
                    />
                </x-field>

                <x-field
                    class="mt-4"
                    x-show="recovery"
                    x-cloak
                >
                    <x-label for="recovery_code">{{ __('Recovery Code') }}</x-label>
                    <x-inputs.tfa-recovery
                        name="recovery_code"
                        x-ref="recovery_code"
                        x-bind:required="recovery"
                    />
                </x-field>

                <x-buttons class="mt-4">

                    <x-buttons.primary>
                        {{ __('buttons.login') }}
                    </x-buttons.primary>

                    <x-buttons.text
                        x-bind:class="{'inline-flex': !recovery, 'hidden': recovery}"
                        x-on:click="
                            recovery = true;
                            $nextTick(() => { $refs.recovery_code.focus() })
                        "
                    >
                        {{ __('auth.tfa.use-recovery-button') }}
                    </x-buttons.text>

                    <x-buttons.text
                        x-bind:class="{'inline-flex': recovery, 'hidden': !recovery}"
                        x-on:click="
                            recovery = false;
                            $nextTick(() => { $refs.code.focus() })
                        "
                        x-cloak
                    >
                        {{ __('auth.tfa.use-code-button') }}
                    </x-buttons.text>

                </x-buttons>
            </x-form>

        </div>
    </x-auth-box>
</x-layouts.guest>
